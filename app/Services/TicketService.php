<?php

namespace App\Services;

use App\Enums\SenderTypeEnum;
use App\Enums\TicketStatusEnum;
use App\Events\TicketReplied;
use App\Events\TicketSubmitted;
use App\Exceptions\InvalidTicketDataException;
use App\Models\Reply;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\AdminReplied;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class TicketService
{
    public function __construct(protected ValidatorFactory $validatorFactory)
    {
    }

    public function getTicketsForUser(User $user): LengthAwarePaginator
    {
        return $user->tickets()->latest()->paginate();
    }

    public function getAllTickets(): LengthAwarePaginator
    {
        return Ticket::latest()->paginate();
    }

    /**
     * @throws InvalidTicketDataException|Throwable
     */
    public function createTicket(User $user, array $data, ?UploadedFile $attachment): Ticket
    {
        $validator = $this->validatorFactory->make($data, [
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            throw new InvalidTicketDataException($validator->errors()->toJson());
        }

        return DB::transaction(function () use ($user, $data, $attachment) {
            $senderType = $user->hasRole(['super_admin', 'support'])
                ? SenderTypeEnum::ADMIN
                : SenderTypeEnum::USER;

            $ticket = Ticket::create([
                'user_id' => $user->id,
                'title' => $data['title'],
                'message' => $data['message'],
                'status' => TicketStatusEnum::OPEN,
                'sender_type' => $senderType,
            ]);

            if ($attachment) {
                $ticket->addMedia($attachment)->toMediaCollection('attachments');
            }

            broadcast(new TicketSubmitted($ticket))->toOthers();

            return $ticket;
        });
    }

    /**
     * @throws InvalidTicketDataException|Throwable
     */
    public function updateTicket(Ticket $ticket, array $data): Ticket
    {
        $validator = $this->validatorFactory->make($data, [
            'title' => ['sometimes', 'string', 'max:255'],
            'message' => ['sometimes', 'string', 'max:5000'],
            'status' => ['sometimes', Rule::in(TicketStatusEnum::values())],
        ]);

        if ($validator->fails()) {
            throw new InvalidTicketDataException($validator->errors()->toJson());
        }

        $ticket->update($data);

        return $ticket->fresh();
    }

    /**
     * @throws InvalidTicketDataException|Throwable
     */
    public function createReply(User $user, Ticket $ticket, array $data, ?UploadedFile $attachment): Reply
    {
        $validator = $this->validatorFactory->make($data, [
            'message' => ['required', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            throw new InvalidTicketDataException($validator->errors()->toJson());
        }

        return DB::transaction(function () use ($user, $ticket, $data, $attachment) {
            $senderType = $user->hasRole(['super_admin', 'support'])
                ? SenderTypeEnum::ADMIN
                : SenderTypeEnum::USER;

            $reply = $ticket->replies()->create([
                'user_id' => $user->id,
                'message' => $data['message'],
                'sender_type' => $senderType,
            ]);

            if ($attachment) {
                $reply->addMedia($attachment)->toMediaCollection('attachments');
            }

            if ($senderType === SenderTypeEnum::ADMIN) {
                $ticket->status = TicketStatusEnum::IN_PROGRESS;
                $ticket->save();
                $ticket->user->notify(new AdminReplied($ticket, $reply));
            } else {
                $ticket->save();
            }


            broadcast(new TicketReplied($ticket, $reply))->toOthers();

            return $reply;
        });
    }
}
