<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use UnitEnum;

class SiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected static ?string $title = 'Pengaturan Situs';

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->content->fill([
            'site_name' => SiteSetting::get('site_name'),
            'contact_email' => SiteSetting::get('contact_email'),
            'contact_phone' => SiteSetting::get('contact_phone'),
            'address' => SiteSetting::get('address'),
            'footer_text' => SiteSetting::get('footer_text'),
            'meta_description' => SiteSetting::get('meta_description'),
            'google_maps_embed' => SiteSetting::get('google_maps_embed'),
            'whatsapp_number' => SiteSetting::get('whatsapp_number'),
            'hero_title' => SiteSetting::get('hero_title'),
            'hero_subtitle' => SiteSetting::get('hero_subtitle'),
            'about_story' => SiteSetting::get('about_story'),
            'social_links' => SiteSetting::get('social_links'),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('site_name')
                    ->label('Nama Situs')
                    ->maxLength(255),

                TextInput::make('contact_email')
                    ->label('Email Kontak')
                    ->email()
                    ->maxLength(255),

                TextInput::make('contact_phone')
                    ->label('Nomor Telepon Kontak')
                    ->tel()
                    ->maxLength(255),

                TextInput::make('whatsapp_number')
                    ->label('Nomor WhatsApp Admin')
                    ->helperText('Dipakai untuk redirect inquiry, mis. 6281234567890.')
                    ->tel()
                    ->maxLength(255),

                Textarea::make('address')
                    ->label('Alamat')
                    ->columnSpanFull(),

                Textarea::make('footer_text')
                    ->label('Teks Footer')
                    ->columnSpanFull(),

                Textarea::make('meta_description')
                    ->label('Meta Deskripsi')
                    ->columnSpanFull(),

                TextInput::make('google_maps_embed')
                    ->label('Google Maps Embed URL')
                    ->url()
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('hero_title')
                    ->label('Judul Hero (Landing)')
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('hero_subtitle')
                    ->label('Subjudul Hero (Landing)')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('about_story')
                    ->label('Cerita Perusahaan (Halaman Tentang)')
                    ->rows(5)
                    ->columnSpanFull(),

                Textarea::make('social_links')
                    ->label('Tautan Sosial Media (satu per baris: Label | URL)')
                    ->helperText('Contoh: Instagram | https://instagram.com/lombokhorizon')
                    ->rows(4)
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->action(function (): void {
                    $userId = auth()->id();

                    foreach ($this->content->getState() as $key => $value) {
                        SiteSetting::set($key, $value, $userId);
                    }

                    Notification::make()
                        ->success()
                        ->title('Pengaturan situs berhasil diperbarui.')
                        ->send();
                }),
        ];
    }

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';
}
