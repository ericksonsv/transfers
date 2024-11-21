<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\Action;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')->schema([
                    Forms\Components\TextInput::make('business_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('tradename')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('rnc')
                        ->type('number')
                        ->minLength(9),
                    Forms\Components\Toggle::make('is_active')
                        ->required(),
                ])->aside(),

                Section::make('Company Logo')->schema([
                    Forms\Components\FileUpload::make('logo_url')->label(false)
                ])->aside(),

                Section::make('Additional Phones')->schema([
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
                        ->addActionLabel('Add phone')
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
                Tables\Columns\ImageColumn::make('logo_url')
                    ->height(40)
                    ->label(__('Logo'))
                    ->defaultImageUrl(url('/images/logos/placeholder.jpg')),
                Tables\Columns\TextColumn::make('business_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tradename')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customers_count')->counts('customers'),
                Tables\Columns\TextColumn::make('rnc')
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
                Action::make('logs')
                    ->url(fn ($record) => CompanyResource::getUrl('activities', ['record' => $record]))
                    ->icon('heroicon-o-clock')
                    ->color('primary')
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
                    Infolists\Components\TextEntry::make('business_name'),
                    Infolists\Components\TextEntry::make('tradename'),
                    Infolists\Components\TextEntry::make('rnc'),
                    Infolists\Components\IconEntry::make('is_active')->boolean(),
                ])->columns([
                    'sm' => 1,
                    'md' => 2,
                ])->aside(),
                ComponentsSection::make('Company Logo')->schema([
                    Infolists\Components\ImageEntry::make('logo_url')
                        ->defaultImageUrl(url('/images/logos/placeholder.jpg'))
                        ->height(100)
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'activities' => Pages\CompanyActivities::route('/{record}/activities'),
            'view' => Pages\ViewCompany::route('/{record}'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
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
