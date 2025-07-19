<div class="mt-8 space-y-6">
    @forelse($this->replies as $reply)
        <div class="bg-gray-50 p-4 rounded-lg shadow">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    {{-- Placeholder for user avatar --}}
                    <span class="inline-block h-10 w-10 rounded-full overflow-hidden bg-gray-100">
                        <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.997A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                </div>
                <div class="ms-4">
                    <div class="text-sm font-medium text-gray-900">{{ $reply->user->name }}</div>
                    <div class="text-sm text-gray-500">{{ $reply->created_at->diffForHumans() }}</div>
                    <p class="mt-1 text-gray-700">
                        {{ $reply->message }}
                    </p>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-gray-500">
            <p>{{ __('messages.no_replies_found') }}</p>
        </div>
    @endforelse
</div>
