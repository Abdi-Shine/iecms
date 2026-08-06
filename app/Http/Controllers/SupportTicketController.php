<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupportTicketController extends Controller
{
    public const CATEGORIES = ['Technical Issue', 'Account Access', 'Bug Report', 'Feature Request', 'Other'];
    public const PRIORITIES = ['Low', 'Medium', 'High'];
    public const STATUSES = ['Open', 'In Progress', 'Waiting for User Response', 'Resolved', 'Closed'];
    public const PER_PAGE_OPTIONS = [10, 25, 50, 100];
    public const ATTACHMENT_RULES = 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip';

    public function index(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = !$user->group_id;

        $base = SupportTicket::query();
        if (!$isSuperAdmin) {
            $base->where('user_id', $user->id);
        }

        $stats = [
            'total'       => (clone $base)->count(),
            'open'        => (clone $base)->where('status', 'Open')->count(),
            'in_progress' => (clone $base)->where('status', 'In Progress')->count(),
            'waiting'     => (clone $base)->where('status', 'Waiting for User Response')->count(),
            'resolved'    => (clone $base)->where('status', 'Resolved')->count(),
            'closed'      => (clone $base)->where('status', 'Closed')->count(),
        ];

        $query = (clone $base)->with(['user', 'assignee'])->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ticket_number', 'like', "%$s%")
                  ->orWhere('title', 'like', "%$s%");
            });
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $perPage = $this->resolvePerPage($request, self::PER_PAGE_OPTIONS);

        $tickets = $query->paginate($perPage)->withQueryString();

        return view('support.support_tickets_index', [
            'tickets'        => $tickets,
            'stats'          => $stats,
            'isSuperAdmin'   => $isSuperAdmin,
            'categories'     => self::CATEGORIES,
            'priorities'     => self::PRIORITIES,
            'statuses'       => self::STATUSES,
            'perPage'        => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    public function create()
    {
        return view('support.support_ticket_create', [
            'categories' => self::CATEGORIES,
            'priorities' => self::PRIORITIES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'category'      => 'required|in:' . implode(',', self::CATEGORIES),
            'priority'      => 'required|in:' . implode(',', self::PRIORITIES),
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => self::ATTACHMENT_RULES,
        ]);

        $user = $request->user();
        $ticket = SupportTicket::create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'category'    => $data['category'],
            'priority'    => $data['priority'],
            'user_id'     => $user->id,
            'status'      => 'Open',
        ]);

        $this->storeAttachments($request, $ticket, $user, null);

        $this->logActivity($ticket, $user, 'created', "{$user->name} submitted this ticket.");

        return redirect()->route('support-tickets.show', $ticket->id)
            ->with('success', 'Ticket submitted successfully.');
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $isSuperAdmin = !$user->group_id;

        $ticket = SupportTicket::with(['user', 'assignee', 'attachments', 'replies.user', 'replies.attachments', 'activities.user'])
            ->findOrFail($id);

        if (!$isSuperAdmin && $ticket->user_id !== $user->id) {
            abort(403);
        }

        $staff = $isSuperAdmin ? User::orderBy('name')->get() : collect();

        return view('support.support_ticket_show', [
            'ticket'       => $ticket,
            'isSuperAdmin' => $isSuperAdmin,
            'staff'        => $staff,
            'statuses'     => self::STATUSES,
        ]);
    }

    public function reply(Request $request, $id)
    {
        $user = $request->user();
        $isSuperAdmin = !$user->group_id;

        $ticket = SupportTicket::findOrFail($id);
        if (!$isSuperAdmin && $ticket->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'message'       => 'required|string',
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => self::ATTACHMENT_RULES,
        ]);

        $reply = $ticket->replies()->create([
            'user_id' => $user->id,
            'message' => $data['message'],
        ]);

        $this->storeAttachments($request, $ticket, $user, $reply->id);

        $who = $isSuperAdmin ? 'Admin' : $user->name;
        $this->logActivity($ticket, $user, 'replied', "{$who} replied to this ticket.");

        $ticket->touch();

        return back()->with('success', 'Reply sent.');
    }

    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        if ($user->group_id) {
            abort(403, 'Only the Super Admin can update ticket status.');
        }

        $ticket = SupportTicket::findOrFail($id);

        $data = $request->validate([
            'status'      => 'required|in:' . implode(',', self::STATUSES),
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($ticket->status !== $data['status']) {
            $this->logActivity($ticket, $user, 'status_changed', "Status changed from \"{$ticket->status}\" to \"{$data['status']}\".");
        }

        $newAssignee = $data['assigned_to'] ?? null;
        if ($newAssignee != $ticket->assigned_to) {
            $assigneeName = $newAssignee ? User::find($newAssignee)?->name : null;
            $description = $assigneeName ? "Ticket assigned to {$assigneeName}." : 'Ticket unassigned.';
            $this->logActivity($ticket, $user, 'assigned', $description);
        }

        $ticket->status = $data['status'];
        $ticket->assigned_to = $newAssignee;
        if (in_array($data['status'], ['Resolved', 'Closed']) && !$ticket->resolved_at) {
            $ticket->resolved_at = now();
        } elseif (!in_array($data['status'], ['Resolved', 'Closed'])) {
            $ticket->resolved_at = null;
        }
        $ticket->save();

        return back()->with('success', 'Ticket updated.');
    }

    public function downloadAttachment(Request $request, $id)
    {
        $user = $request->user();
        $isSuperAdmin = !$user->group_id;

        $attachment = SupportTicketAttachment::with('ticket')->findOrFail($id);

        if (!$isSuperAdmin && $attachment->ticket->user_id !== $user->id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->original_name);
    }

    private function storeAttachments(Request $request, SupportTicket $ticket, User $user, ?int $replyId): void
    {
        if (!$request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            $path = $file->store('support_tickets/' . $ticket->id, 'public');

            SupportTicketAttachment::create([
                'support_ticket_id'       => $ticket->id,
                'support_ticket_reply_id' => $replyId,
                'uploaded_by'             => $user->id,
                'original_name'           => $file->getClientOriginalName(),
                'file_path'               => $path,
                'mime_type'               => $file->getClientMimeType(),
                'size'                    => $file->getSize(),
            ]);
        }
    }

    private function logActivity(SupportTicket $ticket, ?User $user, string $type, string $description): void
    {
        $ticket->activities()->create([
            'user_id'     => $user?->id,
            'type'        => $type,
            'description' => $description,
        ]);
    }
}
