<?php

namespace App\ContentModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Contact extends Model {
    use HasFactory;

    protected $fillable = [
        "title",
        "message",
        "user_id",
        "name",
        "email",
        "phone",
        "seen",
        "contact_type_id",
        'source'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class,);
    }

    public function type(): BelongsTo {
        return $this->belongsTo(ContactType::class,'contact_type_id');
    }
}
