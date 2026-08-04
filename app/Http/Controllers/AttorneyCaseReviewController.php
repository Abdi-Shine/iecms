<?php

namespace App\Http\Controllers;

use App\Models\AttorneyCase;
use App\Models\AttorneyCaseReview;
use App\Models\AttorneyDepartment;
use Illuminate\Http\Request;

class AttorneyCaseReviewController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = AttorneyCase::query();

        $stats = [
            'total'      => (clone $baseQuery)->count(),
            'reviewed'   => (clone $baseQuery)->whereHas('reviews')->count(),
            'unreviewed' => (clone $baseQuery)->whereDoesntHave('reviews')->count(),
        ];

        $query = (clone $baseQuery)->with(['complainants', 'latestReview.department'])->orderByDesc('ACID');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('complainants', function ($cq) use ($search) {
                      $cq->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('review_status')) {
            if ($request->review_status === 'reviewed') {
                $query->whereHas('reviews');
            } elseif ($request->review_status === 'unreviewed') {
                $query->whereDoesntHave('reviews');
            }
        }

        $perPage = $this->resolvePerPage($request);
        $cases   = $query->paginate($perPage)->withQueryString();

        return view('attorney.Direct_Complaint.direct_complaint_review_list', [
            'cases'       => $cases,
            'stats'       => $stats,
        ]);
    }

    public function show(Request $request, $id)
    {
        $case = AttorneyCase::with(['complainants', 'reviews.department', 'reviews.user'])->findOrFail($id);

        return view('attorney.Direct_Complaint.direct_complaint_review', [
            'case'        => $case,
            'departments' => AttorneyDepartment::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'attorney_case_id'         => 'required|exists:attorney_cases,ACID',
            'attorney_department_id'   => 'required|exists:attorney_departments,id',
            'review_date'              => 'required|date',
            'comment'                  => 'required|string',
        ]);

        $review = AttorneyCaseReview::create([
            'attorney_case_id'       => $data['attorney_case_id'],
            'attorney_department_id' => $data['attorney_department_id'],
            'user_id'                => $request->user()->id,
            'review_date'            => $data['review_date'],
            'comment'                => $data['comment'],
        ]);

        $case = AttorneyCase::find($data['attorney_case_id']);
        $department = AttorneyDepartment::find($data['attorney_department_id']);
        $oldStatus = $case?->status;

        $case?->update(['status' => 'Hubin']);

        $case?->activities()->create([
            'user_id'     => $request->user()->id,
            'type'        => 'reviewed',
            'description' => "{$request->user()->name} dib ayuu u eegay dacwadan oo u qoondeeyay {$department?->name}. Xaaladda waxaa laga beddelay \"{$oldStatus}\" una beddelay \"Hubin\".",
        ]);

        return response()->json(['message' => 'Eegista waa la keydiyay.', 'review' => $review->load(['department', 'user'])]);
    }

    public function update(Request $request, $id)
    {
        $review = AttorneyCaseReview::findOrFail($id);

        $data = $request->validate([
            'attorney_department_id' => 'required|exists:attorney_departments,id',
            'review_date'            => 'required|date',
            'comment'                => 'required|string',
        ]);

        $review->update($data);

        return response()->json(['message' => 'Eegista waa la cusboonaysiiyay.', 'review' => $review->load(['department', 'user'])]);
    }

    public function destroy($id)
    {
        $review = AttorneyCaseReview::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Eegista waa la tirtiray.']);
    }
}
