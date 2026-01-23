<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Theamostafa\Wallet\Models\Transaction as BaseTransaction;

class WalletTransaction extends BaseTransaction implements HasMedia
{
    use InteractsWithMedia;

    // This model extends the vendor Transaction model to add media support
}

