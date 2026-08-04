<?php

namespace App\Http\Controllers;

use App\Models\CivilCaseHandover;
use App\Models\DistrictCriminalHandover;
use App\Models\DistrictCriminalHearing;
use App\Models\DistrictExecutionHandover;
use App\Models\DistrictExecutionHearing;
use App\Models\DistrictFamilyHandover;
use App\Models\DistrictFamilyHearing;
use App\Models\DocumentSignature;
use App\Models\Employee;
use App\Models\Hearing;
use App\Models\JudgmentDocumentSignature;
use App\Models\User;
use App\Notifications\CriminalStampApprovedNotification;
use App\Notifications\CriminalStampRequestedNotification;
use App\Notifications\ExecutionStampApprovedNotification;
use App\Notifications\ExecutionStampRequestedNotification;
use App\Notifications\FamilyStampApprovedNotification;
use App\Notifications\FamilyStampRequestedNotification;
use App\Notifications\StampApprovedNotification;
use App\Notifications\StampRequestedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DocumentSignatureController extends Controller
{
    public function sign(Request $request, string $type, int $documentId)
    {
        $request->validate([
            'password' => 'required|string',
            'role'     => 'required|string|in:registrar,clerk,signer,kaaliye,archive_officer,judge',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Erayga sirta ah waa khalad.',
            ], 422);
        }

        $employee = auth()->user()->employee
            ?? Employee::where('EmpName', auth()->user()->name)->first()
            ?? Employee::where('email', auth()->user()->email)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Magacaagu (' . auth()->user()->name . ') kuma jiro diiwaanka shaqaalaha.',
            ], 422);
        }

        if (!$employee->signature && $request->role !== 'archive_officer' && $type !== 'handover') {
            return response()->json([
                'success' => false,
                'message' => 'Fadlan marka hore saxiixa ka geli bogga xogta shaqaalaha.',
            ], 422);
        }

        if (in_array($type, ['judgment_stamp', 'family_judgment_stamp', 'execution_judgment_stamp', 'criminal_judgment_stamp'])) {
            // Validate workflow: archive_officer can only approve after kaaliye has signed
            if ($request->role === 'archive_officer') {
                $hasRequest = JudgmentDocumentSignature::where('document_type', $type)
                    ->where('document_id', $documentId)->where('role', 'kaaliye')->exists();
                if (!$hasRequest) {
                    return response()->json(['success' => false, 'message' => 'Ma jiro codsi la sugayo.'], 422);
                }
            }
            // Write to the unified signatures table
            JudgmentDocumentSignature::updateOrCreate(
                [
                    'document_type' => $type,
                    'document_id'   => $documentId,
                    'signer_id'     => $employee->AID,
                ],
                [
                    'role'         => $request->role,
                    'signed_at'    => now(),
                    'ip_address'   => $request->ip(),
                    'content_hash' => $this->computeHash($type, $documentId),
                ]
            );

            return response()->json(['success' => true, 'message' => 'Saxiixa si guul leh ayaa lagu keydsaday.']);
        }

        DocumentSignature::updateOrCreate(
            [
                'document_type' => $type,
                'document_id'   => $documentId,
                'signer_id'     => $employee->AID,
            ],
            [
                'role'         => $request->role,
                'signed_at'    => now(),
                'ip_address'   => $request->ip(),
                'content_hash' => $this->computeHash($type, $documentId),
            ]
        );

        // Send notifications for the stamp approval workflow
        if (in_array($type, ['hearing_stamp', 'family_hearing_stamp', 'execution_hearing_stamp', 'criminal_hearing_stamp'])) {
            $this->sendStampNotification($request->role, $documentId, $employee, $type);
        }

        return response()->json([
            'success' => true,
            'message' => 'Saxiixa si guul leh ayaa lagu keydsaday.',
        ]);
    }

    private function sendStampNotification(string $role, int $hearingId, Employee $signer, string $type = 'hearing_stamp'): void
    {
        $hearing = match ($type) {
            'family_hearing_stamp'    => DistrictFamilyHearing::with('familyCase')->find($hearingId),
            'execution_hearing_stamp' => DistrictExecutionHearing::with('executionCase')->find($hearingId),
            'criminal_hearing_stamp'  => DistrictCriminalHearing::with('criminalCase')->find($hearingId),
            default                   => Hearing::with('civilCase')->find($hearingId),
        };
        if (!$hearing) return;

        if ($role === 'kaaliye') {
            // Notify all Archive Officers that a stamp request has been submitted
            $archiveUsers = User::whereHas('group.roles.permissions', function ($q) {
                $q->where('module', 'Archive')->where('action', 'view');
            })->orWhereNull('group_id')->get();

            foreach ($archiveUsers as $user) {
                /** @var User $user */
                $user->notify(match ($type) {
                    'family_hearing_stamp'    => new FamilyStampRequestedNotification($hearing, $signer->EmpName),
                    'execution_hearing_stamp' => new ExecutionStampRequestedNotification($hearing, $signer->EmpName),
                    'criminal_hearing_stamp'  => new CriminalStampRequestedNotification($hearing, $signer->EmpName),
                    default                   => new StampRequestedNotification($hearing, $signer->EmpName),
                });
            }
        } elseif ($role === 'archive_officer') {
            $kaaliyeSig = DocumentSignature::with('signer')
                ->where('document_type', $type)
                ->where('document_id', $hearingId)
                ->where('role', 'kaaliye')
                ->first();

            if ($kaaliyeSig?->signer?->system_username) {
                $kaaliyeUser = User::where('email', $kaaliyeSig->signer->system_username)->first();
                $kaaliyeUser?->notify(match ($type) {
                    'family_hearing_stamp'    => new FamilyStampApprovedNotification($hearing, $signer->EmpName),
                    'execution_hearing_stamp' => new ExecutionStampApprovedNotification($hearing, $signer->EmpName),
                    'criminal_hearing_stamp'  => new CriminalStampApprovedNotification($hearing, $signer->EmpName),
                    default                   => new StampApprovedNotification($hearing, $signer->EmpName),
                });
            }
        }
    }

    private function computeHash(string $type, int $documentId): string
    {
        $payload = $type . '|' . $documentId;

        if ($type === 'handover') {
            $doc = CivilCaseHandover::find($documentId);
            $payload .= '|' . ($doc?->civil_case_id ?? '') . '|' . ($doc?->status ?? '');
        } elseif ($type === 'family_handover') {
            $doc = DistrictFamilyHandover::find($documentId);
            $payload .= '|' . ($doc?->family_case_id ?? '') . '|' . ($doc?->status ?? '');
        } elseif ($type === 'execution_handover') {
            $doc = DistrictExecutionHandover::find($documentId);
            $payload .= '|' . ($doc?->execution_case_id ?? '') . '|' . ($doc?->status ?? '');
        } elseif ($type === 'criminal_handover') {
            $doc = DistrictCriminalHandover::find($documentId);
            $payload .= '|' . ($doc?->criminal_case_id ?? '') . '|' . ($doc?->status ?? '');
        }

        return hash('sha256', $payload);
    }
}
