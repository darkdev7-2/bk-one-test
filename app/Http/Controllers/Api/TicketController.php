<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketMessageResource;
use App\Http\Resources\TicketResource;
use App\Models\Message;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function __construct(
        private TicketService $ticketService
    ) {}

    public function index()
    {
        $from_date = request('from_date');
        $to_date = request('to_date');

        $tickets = Ticket::where('user_id', Auth::id())
            ->latest()
            ->when(request('subject'), function ($query) {
                $query->where('title', 'LIKE', '%'.request('subject').'%')
                    ->orWhere('uuid', 'LIKE', '%'.request('subject').'%');
            })
            ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
                $query->whereDate('created_at', '>=', Carbon::parse($from_date)->format('Y-m-d'))
                    ->whereDate('created_at', '<=', Carbon::parse($to_date)->format('Y-m-d'));
            })
            ->paginate();

        return response()->json([
            'status' => true,
            'data' => TicketResource::collection($tickets),
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        try {
            $this->ticketService->validate($request);

            $this->ticketService->store($request);

            return response()->json([
                'status' => true,
                'message' => __('Your ticket was created successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(string $id)
    {
        $ticket = Ticket::uuid($id);

        $messages = Message::where('ticket_id', $ticket->id)->oldest('id')->get();

        return response()->json([
            'status' => true,
            'data' => [
                'ticket' => new TicketResource($ticket),
                'messages' => TicketMessageResource::collection($messages),
            ],
        ]);
    }

    public function reply(Request $request, string $id)
    {
        try {

            $request->merge(['uuid' => $id]);

            $this->ticketService->reply($request);

            return response()->json([
                'status' => true,
                'message' => __('Your ticket reply successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function action(string $uuid)
    {
        try {

            $ticket = Ticket::uuid($uuid);
            $message = '';

            if ($ticket->isClosed()) {
                $ticket->reopen();
                $message = __('Your ticket reopened successfully');
            } else {
                $ticket->close();
                $message = __('Your ticket closed successfully');
            }

            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
