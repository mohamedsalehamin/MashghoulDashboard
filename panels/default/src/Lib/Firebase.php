<?php

namespace App\DefaultPanel\Lib;

use Google\Client;
use Illuminate\Support\Facades\Http;

class Firebase {

    private array $headers = [];
    private array $tokens = [];
    private array $moreData = [];
    private string $authorizationKey = '';
    private int $type = 1;
    private string $title = '';
    private string $body = '';
    private bool $isSent = false;


    public function __destruct() {
        if (!$this->isSent) {
            $this->do();
        }
    }

    static public function make() {
        return new self();
    }

    function do(){
        $response = null;
        foreach ($this->getTokens() as $token) {

            $data = $this->getFields();
            $data['message']['token'] = $token;
            $response = Http::withToken($this->getAccessToken())
                ->post('https://fcm.googleapis.com/v1/projects/mashghoul-1c31b/messages:send',
                    $data
                );

        }

        $this->isSent = true;
        return $response;
    }

    private function getAccessToken() {
        $credentialsPath = storage_path('app/mashghoul-1c31b-firebase-adminsdk-lp8iv-196651a897.json'); // Path to your service account file

        $client = new  Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $token = $client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
    }

    /**
     * @param mixed $authorizationKey
     * @return Firebase
     */
    public function setAuthorizationKey($authorizationKey) {
        $this->authorizationKey = $authorizationKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationKey() {
        $settings = new ThirdPartySettings();
        if (!is_null($this->authorizationKey) && !trim($this->authorizationKey)) {
            $this->authorizationKey = $settings->firebase_server_key;
        }
        return $this->authorizationKey;
    }

    /**
     * @return array
     */

    /**
     * @param int $type
     * @return Firebase
     */
    public function setType(int $type): Firebase {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * @param string $title
     * @return Firebase
     */
    public function setTitle(string $title): Firebase {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $body
     * @return Firebase
     */
    public function setBody(string $body): Firebase {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string {
        return $this->body;
    }

    /**
     * @param array $tokens
     * @return Firebase
     */
    public function setTokens(array $tokens): Firebase {
        $this->tokens = $tokens;
        return $this;
    }

    /**
     * @return array
     */
    public function getTokens(): array {
        return $this->tokens;
    }

    /**
     * @param array $moreData
     * @return Firebase
     */
    public function setMoreData(array $moreData): Firebase {
        $this->moreData = $moreData;
        return $this;
    }

    public function getFields(): array {
        $fields = [
            'message' => [
                'token' => $this->getTokens()[0] ?? '',

                "webpush" => [
                    'data' => [
                        'title' => $this->getTitle(),
                        'body' => $this->getBody(),
                    ],

                ],
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => [
                        'title' => $this->getTitle(),
                        'body' => $this->getBody(),
//                        'icon' => '@mipmap/ic_notification',
//                        "color" => '#ff0000',
//                        'sound' => 'notification.mp3',
//                        "channel_id" => "playmakerChannelId",
                        'default_vibrate_timings' => true,
                        'default_sound' => true,
                    ],
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'title' => $this->getTitle(),
                        'body' => $this->getBody(),
                        'aps' => [
                            "sound" => "bingbong.aiff",
                            "alert" => [
                                "title" => $this->getTitle(),
                                "body" => $this->getBody()
                            ],
                        ],
                    ],
                ]
            ],


        ];
        if (count($this->getMoreData())) {
            $fields['message']['data'] = [...$fields['message']['data'] ?? [], ...$this->getMoreData()];
        }
        return $fields;
    }

    public function getMoreData(): array {
        return $this->moreData;
    }


}
