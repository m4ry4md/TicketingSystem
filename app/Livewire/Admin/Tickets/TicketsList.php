<?php

namespace App\Livewire\Admin\Tickets;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;

class TicketsList extends Component
{
    use WithPagination;

    public function getListeners()
    {
        return [
            // This listener is now correctly set up
            "echo-private:admin,TicketCreated" => 'refreshList',
        ];
    }

    public function refreshList()
    {
        $this->dispatch('toast', ['message' => 'تیکت جدیدی ثبت شد!']);
        // This line refreshes the component and shows the new ticket on the first page.
        $this->resetPage();
    }

    public function render()
    {
        $tickets = Ticket::with('user')->latest()->paginate(10);

        return view('livewire.admin.tickets.tickets-list', [
            'tickets' => $tickets
        ])->layout('components.layouts.admin');
    }
}
