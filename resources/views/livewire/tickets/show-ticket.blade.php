<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.ticket_details') . ' ' . $ticket->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div>
                {{-- Ticket Details --}}
                <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $ticket->title }}</h2>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-sm font-semibold rounded-md
                                    @switch($ticket->status)
                                        @case(\App\Enums\TicketStatusEnum::OPEN) bg-green-100 text-green-800 @break
                                        @case(\App\Enums\TicketStatusEnum::IN_PROGRESS) bg-yellow-100 text-yellow-800 @break
                                        @case(\App\Enums\TicketStatusEnum::CLOSED) bg-red-100 text-red-800 @break
                                    @endswitch
                                ">
                                    {{ $ticket->status->label() }}
                                </span>
                                <span class="text-gray-500 dark:text-gray-400 text-sm">#{{ $ticket->id }}</span>
                            </div>
                        </div>
                        <p class="mt-4 text-gray-600 dark:text-gray-300">{{ $ticket->message }}</p>

                        {{-- Ticket Attachments --}}
                        @if($ticket->getMedia('attachments')->count() > 0)
                            <div class="mt-4">
                                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.attachments') }}</h3>
                                <div class="flex flex-wrap gap-4">
                                    @foreach($ticket->getMedia('attachments') as $media)
                                        <div class="attachment-item">
                                            @if(Str::startsWith($media->mime_type, 'image/'))
                                                <a href="{{ route('attachments.show', $media) }}" title="{{ __('messages.click_to_download') }}">
                                                    <img src="{{ route('attachments.show', ['media' => $media, 'inline' => true]) }}" alt="{{ $media->name }}" class="w-40 h-40 object-cover rounded-lg shadow-md cursor-pointer hover:opacity-80 transition-opacity">
                                                </a>
                                            @else
                                                <a href="{{ route('attachments.show', $media) }}" class="text-blue-500 hover:underline flex items-center gap-2 p-2 bg-gray-100 rounded-lg">
                                                    <span class="material-symbols-outlined">description</span>
                                                    <span>{{ $media->name }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ __('messages.created_by') }} {{ $ticket->user->name }}</span>
                            <span class="mx-2">|</span>
                            <span>{{ __('messages.created_at') }} {{ $ticket->created_at->format('Y/m/d H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Change Status Section (for Admins) --}}
                @can('change_ticket_status', $ticket)
                    <div class="mt-6 p-6 bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
                        @livewire('admin.tickets.change-ticket-status', ['ticket' => $ticket], key($ticket->id))
                    </div>
                @endcan

                {{-- Replies Section --}}
                <div class="mt-6">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('messages.replies') }}</h3>
                    <div class="space-y-4">
                        @forelse($ticket->replies as $reply)
                            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $reply->message }}</p>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>

                                {{-- Reply Attachments --}}
                                @if($reply->getMedia('attachments')->count() > 0)
                                    <div class="mt-3">
                                        <h4 class="text-md font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('messages.attachments') }}</h4>
                                        <div class="flex flex-wrap gap-4 mt-2">
                                            @foreach($reply->getMedia('attachments') as $media)
                                                <div class="attachment-item">
                                                    @if(Str::startsWith($media->mime_type, 'image/'))
                                                        <a href="{{ route('attachments.show', $media) }}" title="{{ __('messages.click_to_download') }}">
                                                            <img src="{{ route('attachments.show', ['media' => $media, 'inline' => true]) }}" alt="{{ $media->name }}" class="w-32 h-32 object-cover rounded-lg shadow-md cursor-pointer hover:opacity-80 transition-opacity">
                                                        </a>
                                                    @else
                                                        <a href="{{ route('attachments.show', $media) }}" class="text-blue-500 hover:underline flex items-center gap-2 p-2 bg-gray-100 rounded-lg">
                                                            <span class="material-symbols-outlined">description</span>
                                                            <span>{{ $media->name }}</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ __('messages.by') }} {{ $reply->user->name }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                                <p class="text-gray-500 dark:text-gray-400">{{ __('messages.no_replies_yet') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Add Reply Form --}}
                @if($ticket->status !== \App\Enums\TicketStatusEnum::CLOSED)
                    <div class="mt-6">
                        <form wire:submit.prevent="addReply">
                            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
                                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('messages.add_reply') }}</h3>

                                <div>
                                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.your_reply') }}</label>
                                    <textarea id="message" wire:model.live="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200"></textarea>
                                    @error('message') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="mt-4">
                                    <label for="attachment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.attachments') }}</label>
                                    <input type="file" id="attachment" wire:model="attachment" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900 dark:file:text-indigo-300 dark:hover:file:bg-indigo-800"/>
                                    @error('attachment') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror

                                    <div wire:loading wire:target="attachment" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('messages.uploading') }}
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        <span wire:loading.remove wire:target="addReply">{{ __('messages.add_reply_button') }}</span>
                                        <span wire:loading wire:target="addReply">{{ __('messages.sending') }}</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
