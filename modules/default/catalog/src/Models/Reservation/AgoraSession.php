<?php

namespace App\CatalogModule\Models\Reservation;

use App\CatalogModule\Models\Conversation;
use Carbon\Carbon;
use Packages\Agora\AgoraFactory;

trait AgoraSession {
    public function isRunning(): bool
    {
        $startSessionDate = Carbon::parse($this->dateTime);
        return $startSessionDate <= now() && $startSessionDate->addMinutes(20) >= now();
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }

    public function generateVoiceCall(): void {

        $token = AgoraFactory::make()->generateVoiceCall("reservation_" . $this->id, $this->dateTime, 100*24, auth()->id());
        $this->conversation()->updateOrCreate(['reservation_id' => $this->id], ['token' => $token]);
    }

    public function generateChatTokens(): array {
        return [
            'channel' => "reservation_" . $this->id,
            'type' => $this->service_type,
            "client" => [
                "id" => AgoraFactory::make()->fetchUser($this->patient),
                "token" => AgoraFactory::make()->generateChatToken($this->patient, 30),
                "name" => $this->patient->name,
                "avatar" => $this->patient->getFirstMediaUrl()
            ],
            "partner" => [
                "id" => AgoraFactory::make()->fetchUser($this->reservable),
                "token" => AgoraFactory::make()->generateChatToken($this->reservable, 30),
                "name" => $this->reservable->name,
                "avatar" => $this->reservable->getFirstMediaUrl()
            ]
        ];
    }

    public function customerInConversation(): bool
    {
        return !is_null($this->conversation?->customer_start_at);
    }

    public function customerEndConversation(): bool
    {
        return !is_null($this->conversation?->finished_at);
    }

    public function contractorInConversation(): bool
    {
        return !is_null($this->conversation?->contractor_start_at);
    }

    public function agoraSessionCanStart(): bool
    {
        // dd($this->isPaid());
        return $this->isRunning() && $this->isPaid() && is_null($this->conversation?->customer_end_at);
    }
    public function partnerThatNotJoiningTheCallYet()
    {
        if (!$this->conversation->startedByCustomer() && !$this->conversation->startedByContractor()) {
            return null;
        }
        if (!$this->conversation->startedByContractor()) {
            return $this->contractor;
        }
        if (!$this->conversation->startedByCustomer()) {
            return $this->customer;
        }
    }
}
