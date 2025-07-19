<div class="space-y-4">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('messages.title') }}
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('messages.status') }}
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('messages.last_update') }}
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($tickets as $ticket)
                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location.href = '{{ route('tickets.show', $ticket) }}'">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $ticket->title }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{-- We'll use a badge or similar for status later --}}
                        {{ $ticket->status->label() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $ticket->updated_at->diffForHumans() }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        {{ __('messages.no_tickets_found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div>
        {{ $tickets->links() }}
    </div>
</div>
