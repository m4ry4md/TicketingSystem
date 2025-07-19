<?php

namespace App\Livewire\Admin\Tickets;

use App\Enums\TicketStatusEnum;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ChangeTicketStatus extends Component
{
    use AuthorizesRequests;

    public Ticket $ticket;
    public string $status;
    public array $statuses;

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->status = $ticket->status->value;
        $this->statuses = TicketStatusEnum::cases();
    }

    public function updateStatus()
    {
        $this->authorize('update', $this->ticket);

        $validated = $this->validate([
            'status' => ['required', Rule::in($this->statuses)],
        ]);

        $this->ticket->update([
            'status' => TicketStatusEnum::from($validated['status']),
        ]);

        $this->dispatch('toast', ['message' => __('messages.ticket_status_updated_successfully')]);
        $this->dispatch('status-updated');
    }

    public function render()
    {
        return view('livewire.admin.tickets.change-ticket-status');
    }
}
