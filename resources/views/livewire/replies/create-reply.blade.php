<div class="mt-8">
    <form wire:submit.prevent="create">
        <div class="space-y-2">
            <label for="message" class="sr-only">{{ __('messages.your_message') }}</label>
            <textarea wire:model="message" id="message" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('messages.write_your_reply_here') }}"></textarea>
            @error('message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mt-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('messages.submit_reply') }}
            </button>
        </div>
    </form>
</div>
