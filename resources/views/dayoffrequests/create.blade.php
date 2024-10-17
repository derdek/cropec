<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('CreateDayoffRequest') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <form class="m-10" method="POST" action="{{ route('create-dayoff-request') }}">
                    @csrf

                    <div>
                        <label for="dayoff_type_id" :value="__('Dayoff Type')"></label>
                        <select id="dayoff_type_id" class="block mt-1 w-full" name="dayoff_type_id" required>
                            @foreach ($dayoffTypes as $dayoffType)
                                <option value="{{ $dayoffType->id }}">{{ $dayoffType->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <x-label for="date_from" :value="__('Start Date')" />

                        <x-input id="date_from" class="block mt-1 w-full" type="date" name="date_from" required />
                    </div>

                    <div class="mt-4">
                        <x-label for="date_to" :value="__('End Date')" />

                        <x-input id="date_to" class="block mt-1 w-full" type="date" name="date_to" required />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-button class="ml-4">
                            {{ __('Submit') }}
                        </x-button>

                        <x-button class="ml-4">
                            <a href="{{ route('dayoff-requests') }}">Cancel</a>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
