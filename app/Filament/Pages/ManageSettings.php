<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 7;

    protected static string $view = 'filament.pages.manage-settings';

    public function mount(): void 
    {
        $setting = Setting::query()->first()->attributesToArray();

        $this->form->fill($setting);
    }
 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('rnc')->numeric(),
                    TextInput::make('office_phone')
                        ->prefixIcon('heroicon-m-phone')
                        ->mask('(999) 999-9999')
                        ->placeholder('(###) ###-####'),
                    TextInput::make('mobile_phone')
                        ->prefixIcon('heroicon-m-device-phone-mobile')
                        ->mask('(999) 999-9999')
                        ->placeholder('(###) ###-####'),
                    TextInput::make('mail')
                        ->prefixIcon('heroicon-m-envelope')
                        ->email(),
                    Textarea::make('address'),
                ])->aside(),
                Section::make('Logo')->schema([
                    FileUpload::make('logo')
                        ->label(false)
                        ->image()
                        ->imageEditor()
                        ->maxSize(1024)
                        ->downloadable()
                ])->aside(),
                Section::make('Social Information')->schema([
                    TextInput::make('website')
                        ->prefixIcon('heroicon-m-link')
                        ->url(),
                    TextInput::make('facebook')
                        ->prefixIcon('heroicon-m-link')
                        ->url(),
                    TextInput::make('instagram')
                        ->prefixIcon('heroicon-m-link')
                        ->url(),
                    TextInput::make('youtube')
                        ->prefixIcon('heroicon-m-link')
                        ->url(),
                ])->aside(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Back to Dashboard')->url('/admin')
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
            Action::make('Cancel')
                ->button()
                ->outlined()
                ->url('/admin')
        ];
    }

    public function update(): void
    {
        $this->validate();

        try {
            $data = $this->form->getState();
            $setting = Setting::query()->find(1);
            $setting->update($data);
        } catch (Halt $exception) {
            return;
        }

        Notification::make() 
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send(); 
    }
}
