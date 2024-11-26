<?php

namespace Packages\Agora;

use Carbon\Carbon;
use Http;
use Packages\Agora\Features\ChatTokenBuilder2;
use Packages\Agora\Features\RtcTokenBuilder;


class AgoraFactory {

    protected static $appID = null;
    protected static $appCertificate = null;

    public function __construct($appID, $appCertificate) {
        static::$appID = $appID;
        static::$appCertificate = $appCertificate;
    }

    public static function make() {
        static::$appCertificate = config('agora.app_certificate');
        return new static(config('agora.app_id'), config('agora.app_certificate'));
    }

    static public function generateVoiceCall($channelName, $date, $duration, $userID): string {
        $date = Carbon::parse($date);
        $currentTimestamp = strtotime($date);
        $channelName = (string)$channelName;
        $user = 'user_' . $userID;
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = ($duration * 60) + (60 * 60);
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        return RtcTokenBuilder::buildTokenWithUid(self::$appID, self::$appCertificate, $channelName, null, $role, $privilegeExpiredTs);
    }

    static public function generateChatToken($user, $expire_at = 3600) {
        return ChatTokenBuilder2::buildUserToken(self::$appID, self::$appCertificate, "user_" . $user->id, $expire_at * 60);
    }

    static public function generateAppToken() {
        return ChatTokenBuilder2::buildAppToken(self::$appID, self::$appCertificate, now()->addDays(2)->getTimestamp());
    }

    static public function fetchUser($partner) {
        dispatch(function () use ($partner) {

            $res = Http::withToken(static::generateAppToken())->get("https://a41.chat.agora.io/41831889/1045266/users");
            $user = $res->collect("entities")->filter(fn($_user) => $_user['username'] == "user_" . $partner->id)->first();
            if (!$user) {
                Http::withToken(static::generateAppToken())->post("https://a41.chat.agora.io/41831889/1045266/users", [
                    "username" => "user_" . $partner->id,
                    "password" => "user_" . $partner->id,
                    "nickname" => $partner->name,
                ])->json();
            }
        })->afterResponse();
        return "user_" . $partner->id;
    }

}
