<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Driver;
use App\Models\Company;
use App\Models\Destiny;
use App\Models\Service;
use Filament\Forms\Get;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use App\Tables\Columns\ServicesColumn;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Grouping\Group as GroupingGroup;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Tables\Columns\DriversColumn;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Tables\Actions\BulkAction;
use Livewire\Component;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Company Information')->schema([
                    Forms\Components\Select::make('company_id')
                        // ->relationship('company', 'tradename')
                        ->options(
                            fn (Get $get): Collection => Company::query()
                            ->where('is_active', true)
                            ->pluck('tradename', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required(),
                    Forms\Components\Select::make('customer_id')
                        ->options(
                            fn (Get $get): Collection => Customer::query()
                            ->where('company_id', $get('company_id'))
                            ->where('is_active', true)
                            ->select([DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'id'])
                            ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->label('Customer')
                        ->required(),
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
                        ->addActionLabel('Add Service')
                        ->schema([
                            Hidden::make('user_id')->default(Auth::user()->id),
                            TextInput::make('client'),
                            DatePicker::make('pickup_date')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->minDate(now())
                                ->required()
                                ->prefixIcon('heroicon-m-calendar')
                                ->closeOnDateSelection(),
                            TimePicker::make('pickup_time')->native(true)->prefixIcon('heroicon-m-clock'),
                            TextInput::make('pickup_place')
                                ->required()
                                ->live()
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
                            TextInput::make('flight_number'),
                            TimePicker::make('flight_time')->native(true)->prefixIcon('heroicon-m-clock'),
                            TextInput::make('passengers')->numeric()->required(),
                            TextInput::make('amount')->prefixIcon('heroicon-m-currency-dollar')->numeric()->minValue(0)->nullable(),
                            Select::make('service_currency_id')
                                ->relationship('serviceCurrency', 'currency')
                                ->searchable()
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
                                ->createOptionForm([
                                    Group::make()->schema([
                                        TextInput::make('status')
                                            ->required()
                                    ])->columns(['sm' => 1, 'lg' => 2])
                                ]),
                            Select::make('drivers')
                                ->relationship(
                                    name: 'drivers',
                                    modifyQueryUsing: fn (Builder $query) => $query->where('is_active', true)
                                )
                                ->getOptionLabelFromRecordUsing(fn (Driver $record) => "{$record->first_name} {$record->last_name} {$record->file}")
                                ->preload()
                                ->searchable()
                                ->multiple()
                                ->columnSpanFull(),
                            RichEditor::make('note')->columnSpanFull()
                        ])
                ])->aside()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Service::query())
            ->modifyQueryUsing(function(Builder $query) {
                $data = $query->with([
                    'drivers' => ['phones','mails'],
                    'order' => [
                        'company' => ['phones','mails'],
                        'customer' => ['phones','mails']
                    ]
                ]);
                return $data;
            })
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('pickup_date')->searchable(),
                TextColumn::make('pickup_time')->searchable(),
                TextColumn::make('pickup_place')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('dropoff_place')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('flight_number')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('flight_time')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('passengers')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount')->searchable()->toggleable(isToggledHiddenByDefault: true),
                DriversColumn::make('drivers')->searchable(),
                TextColumn::make('currency')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('note')->html()->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('serviceStatus.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CANCELADO' => 'danger',
                        'COMPLETADO' => 'success',
                        'EN PROCESO' => 'info',
                        'PENDIENTE' => 'warning',
                        default => 'primary'
                    }),
            ])
            ->groups([
                GroupingGroup::make('order.id')
                    ->label('Order #')
                    ->getDescriptionFromRecordUsing(
                        fn (Service $record): string => $record->order->company->tradename.' - '.$record->order->customer->full_name
                    )
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('id', $direction))
                    ->collapsible()
            ])
            ->defaultGroup('order.id')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
                Action::make('print')
                    ->icon('heroicon-o-printer')
                    ->iconButton()
                    ->tooltip(__('Print Service'))
                    ->color('info')
                    ->url(fn (Service $record): string => route('admin.services.print-service', $record))
                    ->openUrlInNewTab(),
                Action::make('print_individual_invoice')
                    ->icon('heroicon-o-document')
                    ->iconButton()
                    ->tooltip(__('Print Invoice'))
                    ->color('info')
                    ->url(fn (Service $record): string => route('admin.services.print-invoice', $record))
                    ->openUrlInNewTab(),
                Action::make('logs')
                    ->url(fn ($record) => OrderResource::getUrl('activities', ['record' => $record]))
                    ->icon('heroicon-o-clock')
                    ->color('primary')
                // Action::make('print_all_invoices')
                //     ->icon('heroicon-o-document-duplicate')
                //     ->iconButton()
                //     ->tooltip(__('Print All Invoice'))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    BulkAction::make('Print All Invoices')
                        ->icon('heroicon-o-document-duplicate')
                        ->action(
                            fn (Collection $records) => redirect()->route('admin.orders.print-all-invoices', $records->first()->order->id)
                        )
                        ->deselectRecordsAfterCompletion()
                ]),
            ])->selectCurrentPageOnly()
            ->checkIfRecordIsSelectableUsing(
                function (Service $record): bool {
                    // get the order instance
                    $order = $record->order;
                    // check if order has more than one serve
                    if ($order->services()->exists() && $order->services()->count() > 1) {
                        return true;
                    }
                    return false;
                }
            );
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'activities' => Pages\OrderActivity::route('/{record}/activities'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
