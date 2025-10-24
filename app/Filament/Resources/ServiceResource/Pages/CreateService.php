<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use Carbon\Carbon;
use App\Models\Order;
use Filament\Actions;
use App\Models\Driver;
use App\Models\Company;
use Filament\Forms\Get;
use App\Models\Customer;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ColorPicker;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ServiceResource;
use Illuminate\Contracts\Database\Eloquent\Builder;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    public function getModel(): string
    {
        return Order::class;
    }

    public function form(Form $form): Form
    {
		return $form
        ->schema([

            Section::make('Company Informationnnnnn')->schema([
                Hidden::make('user_id')->default(Auth::user()->id),
                Select::make('company_id')
                    // ->relationship('company', 'tradename')
                    ->options(
                        fn (Get $get): Collection => Company::query()
                        ->where('is_active', true)
                        ->pluck('tradename', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->label('Compañia'),
                Select::make('customer_id')
                    ->options(
                        fn (Get $get): Collection => Customer::query()
                        ->where('company_id', $get('company_id'))
                        ->where('is_active', true)
                        ->select([DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'id'])
                        ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->label('Customer')
                    ->required()
                    ->label('Empleado'),
            ])->aside()->columns([
                'sm' => 1,
                'lg' => 2
            ]),

            Section::make('Services Details')->schema([
                Repeater::make('services')
                    ->relationship()
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 3
                    ])
                    ->minItems(1)
                    ->cloneable()
                    ->collapsible()
                    ->label(false)
                    ->addActionLabel('Añadir Servicio')
                    ->schema([
                        Hidden::make('user_id')->default(Auth::user()->id),
                        TextInput::make('client')->label('Cliente'),
                        DatePicker::make('pickup_date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->minDate(Carbon::now()->subDays(1))
                            ->required()
                            ->label('Fecha de Recogida')
                            ->prefixIcon('heroicon-m-calendar')
                            ->closeOnDateSelection(),
                        TimePicker::make('pickup_time')
                            ->native(false)
                            ->prefixIcon('heroicon-m-clock')
                            ->label('Hora de Recogida'),
                        TextInput::make('pickup_place')
                            ->required()
                            ->live()
                            ->label('Lugar de Recogida')
                            ->autocomplete('off')
                            ->datalist(function (?string $state) {
                                $options =[];
                                if($state != null and Str::length($state) >= 2) {
                                    $options = DB::table('destinies')->where('destiny','like','%'.$state.'%')
                                        ->get()
                                        ->pluck('destiny')
                                        ->toarray();
                                }
                                return $options;
                            }),
                        TextInput::make('dropoff_place')
                            ->required()
                            ->live()
                            ->label('Lugar de Entrega')
                            ->autocomplete('off')
                            ->datalist(function (?string $state) {
                                $options =[];
                                if($state != null and Str::length($state) >= 2) {
                                    $options = DB::table('destinies')->where('destiny','like','%'.$state.'%')
                                        ->get()
                                        ->pluck('destiny')
                                        ->toarray();
                                }
                                return $options;
                            }),
                        TextInput::make('flight_number')
                            ->label('Número de Vuelo'),
                        TimePicker::make('flight_time')
                            ->native(false)
                            ->label('Hora de Vuelo')
                            ->prefixIcon('heroicon-m-clock'),
                        TextInput::make('passengers')
                            ->numeric()
                            ->label('Pasajeros')
                            ->required(),
                        TextInput::make('amount')
                            ->prefixIcon('heroicon-m-currency-dollar')
                            ->numeric()
                            ->label('Monto')
                            ->minValue(0)
                            ->nullable(),
                        Select::make('service_currency_id')
                            ->relationship('serviceCurrency', 'currency')
                            ->searchable()
                            ->label('Moneda')
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('currency')
                                    ->minLength(3)
                                    ->maxLength(3)
                                    ->required()
                            ]),
                        Select::make('service_type_id')
                            ->relationship('serviceType', 'type')
                            ->searchable()
                            ->label('Tipo de Servicio')
                            ->preload()
                            ->createOptionForm([
                                Group::make()->schema([
                                    TextInput::make('type')
                                        ->required(),
                                    ColorPicker::make('color')
                                        ->required()
                                ])->columns(['sm' => 1, 'lg' => 2])
                            ]),
                        Select::make('service_status_id')
                            ->relationship('serviceStatus', 'status')
                            ->searchable()
                            ->preload()
                            ->label('Estado del Servicio')
                            ->createOptionForm([
                                Group::make()->schema([
                                    TextInput::make('status')
                                        ->required()
                                ])->columns(['sm' => 1, 'lg' => 2])
                            ]),
                        Select::make('drivers')
                            ->relationship(
                                name: 'drivers',
                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('first_name','asc')->where('is_active', true)
                            )
                            ->getOptionLabelFromRecordUsing(fn (Driver $record) => "{$record->first_name} {$record->last_name} {$record->file}")
                            ->preload()
                            ->label('Choferes')
                            ->searchable()
                            ->multiple(),
                        TextInput::make('shiftz')->label('Turno'),
                        Textarea::make('note')->columnSpanFull()->label('Nota')
                    ])
            ])->aside()

        ]);
    }
}
