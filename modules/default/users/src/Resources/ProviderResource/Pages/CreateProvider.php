<?php

namespace App\UsersModule\Resources\ProviderResource\Pages;

use App\UsersModule\Models\Provider as SalonProvider;
use App\UsersModule\Resources\ProviderResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CreateProvider extends CreateRecord
{
    protected static string $resource = ProviderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['phone_verified_at'] = now();

        $draft = new SalonProvider;
        $draft->name = data_get($data, 'provider.name', []);

        $slug = Str::slug(trim((string) data_get($data, 'provider.slug', '')), '-', 'en');
        if ($slug === '') {
            $slug = SalonProvider::generateUniqueSlug($draft, null);
        }

        Validator::make(
            ['slug' => $slug],
            [
                'slug' => [
                    'required',
                    'max:255',
                    Rule::unique('providers', 'slug'),
                ],
            ],
            [],
            ['slug' => __('forms.fields.slug')]
        )->validate();

        data_set($data, 'provider.slug', $slug);

        return $data;
    }

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}
