<?php

namespace App\ContentModule\Models;

use App\DoctorPanel\Filament\Resources\Product;
use App\DefaultPanel\Traits\Publishable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Category extends Model implements HasMedia {
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
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function father(): BelongsTo {
        return $this->belongsTo(Category::class, 'parent_id');
    }


    public function posts() {
        return $this->hasMany(Post::class);
    }


}
