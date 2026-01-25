<?php

namespace App\DefaultPanel\Actions;

use Http;
use App\CatalogModule\Models\Transaction;
use Lorisleiva\Actions\Concerns\AsAction;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;

class GetRefundTransactionStatusAction {
    use AsAction;

    public function handle($key) {
        $response = Http::withToken(config("myfatoorah.api_key"))->post('https://apitest.myfatoorah.com/v2/GetRefundStatus', [
            "Key" => $key,
            "KeyType" => "RefundId"
        ])->json();

        if (!$response['IsSuccess'] && count($response['ValidationErrors'])) {
            return $response['ValidationErrors'][0]['Error']??'';
        }
        return $response['Data']['RefundStatusResult'][0]['RefundStatus'] ?? "N/A";


    }


}
