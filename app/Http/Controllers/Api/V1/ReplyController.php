<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reply\StoreReplyRequest;
use App\Http\Requests\Reply\UpdateReplyRequest;
use App\Http\Resources\Api\V1\ReplyResource;
use App\Models\Reply;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;

class ReplyController extends Controller
{
    use AuthorizesRequests;
    public function __construct(protected TicketService $ticketService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Ticket $ticket)
    {
        $this->authorize('viewAny', [Reply::class, $ticket]);

        $replies = $ticket->replies()->latest()->paginate();

        return ReplyResource::collection($replies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReplyRequest $request, Ticket $ticket)
    {
        $this->authorize('create', [Reply::class, $ticket]);

        $reply = $this->ticketService->createReply(
            $request->user(),
            $ticket,
            $request->validated(),
            $request->file('attachment')
        );

        return (new ReplyResource($reply))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reply $reply)
    {
        $this->authorize('view', $reply);

        return new ReplyResource($reply->load('user', 'media'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReplyRequest $request, Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->update($request->validated());

        return new ReplyResource($reply);
    }
}
