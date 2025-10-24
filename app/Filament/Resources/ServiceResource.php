<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Service;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Resources\ServiceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ServiceResource\RelationManagers;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('user_id')
                //     ->required()
                //     ->numeric(),
                // Forms\Components\Select::make('order_id')
                //     ->relationship('order', 'id')
                //     ->required(),
                // Forms\Components\Select::make('service_currency_id')
                //     ->relationship('serviceCurrency', 'id'),
                // Forms\Components\Select::make('service_type_id')
                //     ->relationship('serviceType', 'id'),
                // Forms\Components\Select::make('service_status_id')
                //     ->relationship('serviceStatus', 'id'),
                // Forms\Components\TextInput::make('client')
                //     ->maxLength(255),
                // Forms\Components\DatePicker::make('pickup_date'),
                // Forms\Components\TextInput::make('pickup_time'),
                // Forms\Components\TextInput::make('pickup_place')
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('dropoff_place')
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('flight_number')
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('flight_time'),
                // Forms\Components\TextInput::make('passengers')
                //     ->numeric(),
                // Forms\Components\TextInput::make('amount')
                //     ->numeric(),
                // Forms\Components\Textarea::make('shiftz')
                //     ->columnSpanFull(),
                // Forms\Components\Textarea::make('note')
                //     ->columnSpanFull(),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        dd($data);

        $data['user_id'] = Auth::user()->id;

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('order.company.tradename')->label('Compañía')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('order.customer.full_name')->label('Cliente')
                    ->sortable(['first_name', 'last_name'])
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('pickup_date')->sortable()->label('Fecha')->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('pickup_time')->sortable()->label('Hora')->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('drivers.first_name')
                    ->getStateUsing(function ($record) {
                        if ($record->drivers->isEmpty()) {
                            return 'Sin Asignar';
                        } else {
                            return $record->drivers->pluck('full_name')->implode(', ');
                        }
                    })
                    ->label('Choferes')
                    ->listWithLineBreaks()
                    ->separator(',')
                    ->badge()
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('serviceStatus.status')
                    ->label('Estado')
                    ->sortable()
                    ->badge()
                    ->default('Sin Asignar')
                    ->color(fn (string $state): string => match ($state) {
                        'CANCELADO' => 'danger',
                        'COMPLETADO' => 'success',
                        'EN PROCESO' => 'info',
                        'PENDIENTE' => 'warning',
                        default => 'primary'
                    }),
            ])
            ->filters([
                TrashedFilter::make()->label('Servicios Eliminados'),
                SelectFilter::make('service_status_id')
                    ->relationship('serviceStatus', 'status')
                    ->label('Estado del Servicio')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Filter::make('Fecha Servicio')
                    ->form([
                        DatePicker::make('pickup_date_from')->label('Fecha Desde'),
                        DatePicker::make('pickup_date_until')->label('Fecha Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['pickup_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('pickup_date', '>=', $date),
                            )
                            ->when(
                                $data['pickup_date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('pickup_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['pickup_date_from'] ?? null) {
                            $indicators[] = Indicator::make('Fecha desde ' . Carbon::parse($data['pickup_date_from'])->toFormattedDateString())
                                ->removeField('pickup_date_from');
                        }

                        if ($data['pickup_date_until'] ?? null) {
                            $indicators[] = Indicator::make('Fecha hasta ' . Carbon::parse($data['pickup_date_until'])->toFormattedDateString())
                                ->removeField('pickup_date_until');
                        }

                        return $indicators;
                    })
            ])
            ->actions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('Editar')),
                Action::make('print')
                    ->icon('heroicon-o-printer')
                    ->iconButton()
                    ->tooltip(__('Imprimir Servicio'))
                    ->color('info')
                    ->url(fn (Service $record): string => route('admin.services.print-service', $record))
                    ->openUrlInNewTab(),
                Action::make('print_individual_invoice')
                    ->icon('heroicon-o-document')
                    ->iconButton()
                    ->tooltip(__('Imprimir Factura Individual'))
                    ->color('info')
                    ->url(fn (Service $record): string => route('admin.services.print-invoice', $record))
                    ->openUrlInNewTab(),
                Action::make('print_all_invoices')
                    ->icon('heroicon-o-document-duplicate')
                    ->iconButton()
                    ->tooltip(__('Imprimir Facturas'))
                    ->color('info')
                    ->url(fn (Service $record): string => route('admin.orders.print-all-invoices', $record->order->id))
                    ->openUrlInNewTab()
                    // ->visible(fn (Service $record): bool => $record->order && $record->order->services()->count() > 1),
                    ->visible(function (Service $record): bool {
                        $order = $record->order;
                        return $order->services()->count() > 1;
                    }),
                Action::make('logs')
                    ->url(fn ($record) => ServiceResource::getUrl('activities', ['record' => $record]))
                    ->iconButton()
                    ->icon('heroicon-o-clock')
                    ->color('primary')
                    ->tooltip(__('Logs')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
            'activities' => Pages\ServiceActivity::route('/{record}/activities'),
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
