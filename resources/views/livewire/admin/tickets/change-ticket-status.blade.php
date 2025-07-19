<div>
    <div>
        <label for="status-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('messages.change_ticket_status') }}
        </label>
        <select wire:model="status" id="status-select" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300">
            @foreach($statuses as $statusEnum)
                <option value="{{ $statusEnum }}">{{ $statusEnum->label() }}</option>
            @endforeach
        </select>
        @error('status') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
    </div>

    <div class="mt-4">
        <button
            wire:click.prevent="updateStatus"
            type="button"
            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <span wire:loading.remove wire:target="updateStatus">{{ __('messages.update_status') }}</span>
            <span wire:loading wire:target="updateStatus">{{ __('messages.sending') }}</span>
        </button>
    </div>
</div>
