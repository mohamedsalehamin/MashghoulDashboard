<?php

namespace App\CatalogModule\Models;

use App\DefaultPanel\Traits\Publishable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Specialization extends Model implements HasMedia {
    use HasTranslations, Publishable, InteractsWithMedia;

    protected $fillable = ['name', 'status', 'parent_id'];
    public $translatable = ['name'];
    use HasFactory;

    public function scopeParent($builder) {
        return $builder->where('parent_id', null);
    }

    public function hasParent() {
        return !is_null($this->parent_id);
    }

    public function scopeChildren($builder) {
        return $builder->where('parent_id', "!=", null);
    }

    public function children() {
        return $this->hasMany(Specialization::class, 'parent_id');
    }

    public function father(): BelongsTo {
        return $this->belongsTo(Specialization::class, 'parent_id');
    }




}
