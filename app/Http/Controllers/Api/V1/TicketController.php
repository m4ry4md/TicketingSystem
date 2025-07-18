<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Ticket\StoreTicketRequest;
use App\Http\Requests\V1\Ticket\UpdateTicketRequest;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * Handles ticket management for the authenticated user.
 */
class TicketController extends Controller
{
    public function __construct(protected TicketService $ticketService)
    {
    }

    /**
     * Display a listing of the authenticated user's tickets.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Fetches tickets specifically for the authenticated user.
        $tickets = $this->ticketService->getTicketsForUser($user);

        return TicketResource::collection($tickets);
    }

    /**
     * Store a newly created ticket for the authenticated user.
     */
    public function store(StoreTicketRequest $request)
    {
        $ticket = $this->ticketService->createTicket(
            $request->user(),
            $request->validated(),
            $request->file('attachment')
        );

        return (new TicketResource($ticket))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified ticket.
     * Authorization is handled by the policy.
     */
    public function show(Ticket $ticket)
    {
        return new TicketResource($ticket->load('user', 'replies.user', 'media', 'replies.media'));
    }

    /**
     * Update the specified ticket.
     * Authorization is handled by the policy.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $updatedTicket = $this->ticketService->updateTicket(
            $ticket,
            $request->validated()
        );

        return new TicketResource($updatedTicket);
    }


}
