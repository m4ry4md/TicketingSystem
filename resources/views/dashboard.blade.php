<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900 flex flex-col items-center justify-center min-h-[300px]">

                    <h3 class="text-2xl font-bold mb-10 text-gray-800">
                        {{ __('messages.dashboard') }}
                    </h3>

                    <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('messages.tickets') }}
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
