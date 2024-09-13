<div>
    {{-- {{ $getState() }} --}}
    @if ($getState() != null)
        <div class="px-3 py-2 flex flex-col space-y-1">
            @foreach ($getState() as $driver)
                <x-filament::badge>
                    {{ $driver->first_name }} {{ $driver->last_name }} {{ $driver->file ?? '' }}
                </x-filament::badge>
            @endforeach
        </div>
    @else
        <div class="px-3 py-2">
            <x-filament::badge color="danger">
                {{ __('Not Available') }}
            </x-filament::badge>
        </div>
    @endif
</div>
