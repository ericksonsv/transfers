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
                    <p>{{ trans('Date') }}: {{ now()->toFormattedDateString() }}</p>
                    <p class="font-bold text-2xl uppercase">{{ trans('Order') }} #{{ $record->id }}</p>
                </td>
            </tr>
        </table>
    </header>

    <table class="w-full mb-6">
        <tr>
            <td>
                <p class="font-bold text-xl text-center uppercase">{{ trans('Invoice') }}</p>
            </td>
        </tr>
    </table>

    <table class="text-sm mb-4">
        <tbody>
            <tr>
                <td class="px-2 text-right font-bold">{{ trans('Client') }}:</td>
                <td class="px-2">{{ $record->client }}</td>
            </tr>
            @if ($record->order->company)
                <tr>
                    <td class="px-2 text-right font-bold">{{ trans('Company') }}:</td>
                    <td class="px-2">{{ $record->order->company->business_name }}</td>
                </tr>
            @endif
            <tr>
                <td class="px-2 text-right font-bold">{{ trans('Passengers') }}:</td>
                <td class="px-2">{{ $record->passengers }}</td>
            </tr>
            @if ($record->flight && $record->flight_time)
                <tr>
                    <td class="px-2 text-right font-bold">{{ trans('Flight') }}:</td>
                    <td class="px-2">{{ $record->flight }}</td>
                </tr>
                <tr>
                    <td class="px-2 text-right font-bold">{{ trans('Flight Time') }}:</td>
                    <td class="px-2">{{ displayTime($record->flight_time) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="w-full text-sm">
        <tbody>
            <tr>
                <td class="border px-2 py-1 text-right font-bold w-1">{{ trans('Date') }}:</td>
                <td class="border px-2 py-1">{{ $record->pickup_date }}</td>
                <td class="border px-2 py-1 text-right font-bold w-1">{{ trans('Time') }}:</td>
                <td class="border px-2 py-1">{{ $record->pickup_time }}</td>
            </tr>
            <tr>
                <td class="border px-2 py-1 text-right font-bold w-1">{{ trans('Pickup') }}:</td>
                <td class="border px-2 py-1">{{ $record->pickup_place }}</td>
                <td class="border px-2 py-1 text-right font-bold w-1">{{ trans('Dropoff') }}:</td>
                <td class="border px-2 py-1">{{ $record->dropoff_place }}</td>
            </tr>
        </tbody>
    </table>

    <table class="w-full text-sm mt-6">
        <tbody>
            <tr>
                <td class="px-2 py-1 font-bold text-right text-lg">
                    Total: {{ $record->serviceCurrency->currency ?? '' }}$ {{ $record->amount }}
                </td>
            </tr>
        </tbody>
    </table>

    @if ($record->note)
        <table class="w-full mt-6 text-sm">
            <tr>
                <td colspan="6" class="border px-2 py-1 font-bold">{{ trans('Note') }}</td>
            </tr>
            <tr>
                <td colspan="6" class="border px-2 py-1">{!! $record->note !!}</td>
            </tr>
        </table>
    @endif
    
</div>
