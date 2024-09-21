<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Driver;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DriverResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as ComponentsSection;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')->schema([
                    
                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('file')->label('Ficha')
                        ->maxLength(5)
                        ->mask('F-999')
                        ->placeholder('F-###')
                        ->live()
                        ->autocomplete('off') 
                        ->datalist(function (?string $state) {
                            $options = [];
                            if($state != null and Str::length($state) >= 1) {
                                $options = DB::table('bus_technical_sheets')->where('technical_sheet','like','%'.$state.'%')
                                    ->get()
                                    ->pluck('technical_sheet')
                                    ->toarray();
                            }
                            return $options; 
                        }),
                    // Forms\Components\TextInput::make('password')
                    //     ->password()
                    //     ->required()
                    //     ->maxLength(255)
                    //     ->default('$2y$12$vKMnsFvBYTDRmJVUf.w5lOMMOYYmBunhbbOjmzXuAEfqKGY.iPXwi'),
                ])->aside(),
                
                Section::make('Image Profile')->schema([
                    Forms\Components\FileUpload::make('avatar_url')->label(false),
                ])->aside(),
                
                Section::make('Status')->schema([
                    Forms\Components\Toggle::make('is_active')->required(),
                ])->aside(),

                Section::make('Add Phone Number')->schema([
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
                        ->addActionLabel('Add phone number')
                        ->collapsible(),
                ])->aside(),
                
                Section::make('Additionals Emails')->schema([
                    Repeater::make('Mails')
                        ->label(false)
                        ->relationship('mails')
                        ->simple(
                            Forms\Components\TextInput::make('mail')
                                ->type('email')
                                ->suffixIcon('heroicon-m-envelope'),
                        )
                        ->maxItems(3)
                        ->addActionLabel('Add mail')
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
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('file')
                    ->label('Ficha')
                    ->placeholder(__('N/A'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phones.phone')
                    ->label('Additional Phones')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->searchable()
                    ->placeholder(__('No Available'))
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('mails.mail')
                    ->label('Additional Emails')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->searchable()
                    ->placeholder(__('No Available'))
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\ToggleColumn::make('is_active'),
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
                    Infolists\Components\IconEntry::make('is_active')->boolean(),
                ])->columns([
                    'sm' => 1,
                    'md' => 2,
                ])->aside(),
                ComponentsSection::make('Image Profile')->schema([
                    Infolists\Components\ImageEntry::make('avatar_url')
                        ->label(false)
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
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'view' => Pages\ViewDriver::route('/{record}'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
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
