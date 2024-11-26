<?php

namespace App\DefaultPanel\Actions\Shared\Authentication;

use App\Utilities\Lib\Action;

class VerifyAltPhoneAction  extends Action
{
    public function __construct($request)
    {
        auth()->user()->update(['phone' => $request->get('phone')]);
        auth()->user()->verificationCodes()->delete();
    }

}
