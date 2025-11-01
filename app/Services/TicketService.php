<?php

namespace App\Services;

use App\Models\Ticket;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TicketService
{
    use ImageUpload, NotifyTrait;

    public function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'message' => 'required',
            'priority' => 'required',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages(['error' => $validator->errors()->first()]);
        }
    }

    public function store($request)
    {
        $user = auth()->user();

        $attachments = [];

        foreach ($request->file('attachments', []) as $attach) {
            $attachments[] = self::imageUploadTrait($attach);
        }

        $data = [
            'uuid' => 'SUPT'.rand(100000, 999999),
            'title' => $request->get('title'),
            'priority' => $request->get('priority'),
            'message' => nl2br($request->get('message')),
            'attachments' => json_encode($attachments),
        ];

        $ticket = $user->tickets()->create($data);

        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[email]]' => $user->email,
            '[[subject]]' => $data['uuid'],
            '[[title]]' => $data['title'],
            '[[message]]' => $data['message'],
            '[[status]]' => 'OPEN',
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
        ];

        $this->mailNotify(setting('support_email', 'global'), 'admin_support_ticket', $shortcodes);
        $this->pushNotify('support_ticket_created', $shortcodes, route('admin.ticket.show', $ticket->uuid), $user->id, 'Admin');

        return $ticket;
    }

    public function reply($request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages(['error' => $validator->errors()->first()]);
        }

        $user = auth()->user();

        $attachments = [];

        foreach ($request->file('attachments', []) as $attach) {
            $attachments[] = self::imageUploadTrait($attach);
        }

        $data = [
            'user_id' => $user->id,
            'message' => nl2br($request->get('message')),
            'attachments' => json_encode($attachments),
        ];

        $ticket = Ticket::uuid($request->get('uuid'));

        $ticket->messages()->create($data);

        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[email]]' => $user->email,
            '[[subject]]' => $request->get('uuid'),
            '[[title]]' => $ticket->title,
            '[[message]]' => $data['message'],
            '[[status]]' => $ticket->status,
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
        ];

        $this->mailNotify(setting('support_email', 'global'), 'admin_support_ticket', $shortcodes);
        $this->pushNotify('support_ticket_reply', $shortcodes, route('admin.ticket.show', $ticket->uuid), $user->id, 'Admin');

        return $ticket;
    }
}
