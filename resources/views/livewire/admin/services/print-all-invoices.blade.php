<div>
    
    <header class="mb-6">
        <table class="w-full">
            <tr>
                <td>
                    <div class="leading-tight text-xs">
                        <img src="{{ asset('storage/'.$setting->logo)}}" alt="logo" class="h-20 mb-2">
                        <p>{{ $setting->address }}</p>
                        <p>{{ trans('Office Phone') }}: {{ $setting->office_phone }}</p>
                        <p>{{ trans('Mobile Phone') }}: {{ $setting->mobile_phone }}</p>
                        <p>{{ trans('Email') }}: {{ $setting->mail }}</p>
                        <p class="font-bold">{{ trans('RNC') }}: {{ $setting->rnc }}</p>
                    </div>
                </td>
                <td class="text-right align-top">
                    <p class="mb-4">{{ trans('Date') }}: {{ now()->toFormattedDateString() }}</p>
                    <p class="font-bold">{{ $record->company->business_name }}</p>
                    <p class="font-bold">{{ $record->customer->full_name }}</p>
                </td>
            </tr>
        </table>
    </header>

    <table class="w-full mb-6">
        <tr>
            <td>
                <p class="font-bold text-xl text-center uppercase">{{ trans('Invoices') }}</p>
            </td>
        </tr>
    </table>

    @foreach ($record->services as $service)
        <table class="w-full text-sm">
            <tbody>
                <tr>
                    <td class="border px-2 py-1 text-right font-bold w-0">{{ trans('Servicio') }}:</td>
                    <td class="border px-2 py-1">#{{ $service->id }}</td>
                    <td class="border px-2 py-1 text-right font-bold w-0">{{ trans('Date') }}:</td>
                    <td class="border px-2 py-1">{{ $service->pickup_date }}</td>
                    <td class="border px-2 py-1 text-right font-bold">{{ trans('Time') }}:</td>
                    <td class="border px-2 py-1">{{ $service->pickup_time }}</td>

                </tr>
                @if ($service->flight && $service->flight_time)
                    <tr>
                        <td class="border px-2 py-1 text-right font-bold w-0">{{ trans('Flight') }}:</td>
                        <td class="border px-2 py-1">{{ $service->flight }}</td>
                        <td class="border px-2 py-1 text-right font-bold w-24">{{ trans('Flight Time') }}:</td>
                        <td class="border px-2 py-1" colspan="3">{{ $service->flight_time }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="border px-2 py-1 text-right font-bold w-0">{{ trans('Pickup') }}:</td>
                    <td class="border px-2 py-1">{{ $service->pickup_place }}</td>
                    <td class="border px-2 py-1 text-right font-bold">{{ trans('Dropoff') }}:</td>
                    <td class="border px-2 py-1">{{ $service->dropoff_place }}</td>
                    <td class="border px-2 py-1 text-right font-bold w-0">{{ trans('Passengers') }}:</td>
                    <td class="border px-2 py-1">{{ $service->passengers }}</td>
                </tr>
            </tbody>
        </table>

        <table class="w-full text-sm mb-6">
            <tbody>
                <tr>
                    <td class="px-2 py-1 text-right">Sub-Total: {{ $service->serviceCurrency->currency }}$ {{ $service->amount }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <table class="w-full text-sm mb-6">
        <tbody>
            <tr>
                <td class="px-2 py-1 font-bold text-right">
                    <p class="underline underline-offset-8">
                        Total: {{ $record->services()->first()->serviceCurrency->currency }}$ {{ $record->services->sum('amount') }}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

</div>
