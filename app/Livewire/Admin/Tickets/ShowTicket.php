<?php

namespace App\Livewire\Admin\Tickets;

use App\Http\Requests\Reply\StoreReplyRequest;
use App\Models\Ticket;
use App\Services\TicketService;
use Livewire\Component;
use Livewire\WithFileUploads;

class ShowTicket extends Component
{
    use WithFileUploads;

    public Ticket $ticket;
    public string $message = '';
    public $attachment;

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * This method enables real-time validation.
     * It runs every time a public property is updated.
     */
    public function updated($propertyName)
    {
        // We pass the rules explicitly to ensure the correct validation runs.
        $this->validateOnly($propertyName, (new StoreReplyRequest())->rules());
    }

    public function getListeners()
    {
        return [
            "echo-private:tickets.{$this->ticket->id},.App.Events.TicketReplied" => 'newReplyAdded',
            'status-updated' => 'refreshTicket',
        ];
    }

    public function refreshTicket()
    {
        $this->ticket->refresh();
    }

    public function addReply(TicketService $ticketService)
    {
        // Validate the form data using the rules from StoreReplyRequest.
        $validatedData = $this->validate((new StoreReplyRequest())->rules());


        $reply = $ticketService->createReply(
            auth()->user(),
            $this->ticket,
            $validatedData,
            $this->attachment
        );

        $this->newReplyAdded($reply->id);
        $this->reset('message', 'attachment');
    }

    public function newReplyAdded($replyId)
    {
        $this->ticket->refresh();
        $this->dispatch('reply-added', 'پاسخ شما با موفقیت ثبت شد.');
    }

    public function render()
    {
        return view('livewire.tickets.show-ticket')
            ->layout('components.layouts.app');
    }
}
