<?php

namespace App\ContentModule\Models;

use App\DefaultPanel\Traits\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ProviderActivity extends Model
{
    use HasTranslations, Publishable;

    protected $guarded = ['id'];

    protected array $translatable = ['name'];

    public function providers(): HasMany
    {
        return $this->hasMany(\App\UsersModule\Models\Provider::class);
    }
}
