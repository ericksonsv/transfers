<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Infolists;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Company;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')->schema([

                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->label('Nombre')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->label('Apellido')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->label('Correo')
                        ->maxLength(255),
                    // Forms\Components\TextInput::make('password')
                    //     ->password()
                    //     ->required()
                    //     ->maxLength(255)
                    //     ->default('$2y$12$vKMnsFvBYTDRmJVUf.w5lOMMOYYmBunhbbOjmzXuAEfqKGY.iPXwi'),
                    Forms\Components\Select::make('company_id')
                        ->options(
                            fn (Get $get): Collection => Company::query()
                            ->where('is_active', true)
                            ->pluck('tradename', 'id')
                        )
                        ->label('Compañía')
                        ->searchable()
                        ->preload()
                        ->required()
                ])->aside(),

                Section::make('Image de Perfil')->schema([
                    Forms\Components\FileUpload::make('avatar_url')->label(false),
                ])->aside(),

                Section::make('Estado')->schema([
                    Forms\Components\Toggle::make('is_active')->required()->label('Activo'),
                ])->aside(),

                Section::make('Agregrar Teléfono')->schema([
                    Repeater::make('Phones')
                        ->label(false)
                        ->relationship('phones')
                        ->simple(
                            Forms\Components\TextInput::make('phone')
                                ->suffixIcon('heroicon-m-phone')
                                ->mask('(999) 999-9999')
                                ->placeholder('(###) ###-####'),
                        )
                        ->maxItems(3)
                        ->defaultItems(0)
                        ->addActionLabel('Agregar')
                        ->collapsible(),
                ])->aside(),

                Section::make('Correos Adicionales')->schema([
                    Repeater::make('Mails')
                        ->label(false)
                        ->relationship('mails')
                        ->simple(
                            Forms\Components\TextInput::make('mail')
                                ->type('email')
                                ->suffixIcon('heroicon-m-envelope'),
                        )
                        ->maxItems(3)
                        ->defaultItems(0)
                        ->addActionLabel('Agregar')
                        ->collapsible()
                ])->aside(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label(__('Avatar'))
                    ->defaultImageUrl(url('/images/avatars/placeholder.png'))
                    ->circular()
                    ->height(30),
                Tables\Columns\TextColumn::make('company.tradename')
                    ->searchable()
                    ->label('Compañía'),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellido')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Primary Email')
                    ->label('Correo Primario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phones.phone')
                    ->label('Telefonos')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->searchable()
                    ->placeholder(__('No Available'))
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('mails.mail')
                    ->label('Correos')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->searchable()
                    ->placeholder(__('No Disponible'))
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\ToggleColumn::make('is_active')->label('Estado'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('logs')
                    ->url(fn ($record) => CustomerResource::getUrl('activities', ['record' => $record]))
                    ->icon('heroicon-o-clock')
                    ->color('primary')
                    ->visible(Auth::user()->can('log_customer'))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make('Basic Information')->schema([
                    Infolists\Components\TextEntry::make('first_name'),
                    Infolists\Components\TextEntry::make('last_name'),
                    Infolists\Components\TextEntry::make('email'),
                    Infolists\Components\TextEntry::make('company.tradename'),
                    Infolists\Components\IconEntry::make('is_active')->boolean(),
                ])->columns([
                    'sm' => 1,
                    'md' => 2,
                ])->aside(),
                ComponentsSection::make('Image Profile')->schema([
                    Infolists\Components\ImageEntry::make('avatar_url')
                        ->label(false)
                        ->defaultImageUrl(url('/images/avatars/placeholder.png'))
                        ->circular()
                ])->aside(),
                ComponentsSection::make('Additional Emails')->schema([
                    Infolists\Components\TextEntry::make('mails.mail')
                        ->listWithLineBreaks()
                        ->bulleted()
                        ->default('Not Available')
                        ->label(false)
                ])->aside(),
                ComponentsSection::make('Additional Phones')->schema([
                    Infolists\Components\TextEntry::make('phones.phone')
                        ->listWithLineBreaks()
                        ->bulleted()
                        ->default('Not Available')
                        ->label(false)
                ])->aside(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'activities' => Pages\CustomerActivities::route('/{record}/activities'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
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
