<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum DoctorReservationStatus: string implements HasLabel {
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PATIENT_RESCHEDULED = 'patient_rescheduled';
    case DOCTOR_RESCHEDULED = 'doctor_rescheduled';
    case COMPLETED = 'completed';
    case PROBLEMATIC = 'problematic';
    case PATIENT_LATE = 'patient_late';
    case DOCTOR_LATE = 'doctor_late';
    case PATIENT_CANCELED = 'patient_canceled';
    case DOCTOR_CANCELED = 'doctor_canceled';

    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'pending','created', => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'doctor_canceled','system_canceled', 'patient_canceled', 'patient_rescheduled', 'doctor_rescheduled', 'problematic', 'patient_late', 'doctor_late' => 'danger',
        };

    }

    public function getClass() {
        return match ($this->value) {
            'pending', => 'waiting',
            'processing' => 'ongoing',
            'completed' => 'completed',
            'patient_rescheduled', 'doctor_rescheduled' => 'rescheduled',
            'problematic', 'patient_late', 'doctor_late' => 'reported',
            'doctor_canceled', 'patient_canceled' => 'canceled'
        };
    }
    public function getIcon(): string {
        return match ($this->value) {
            'created', => 'heroicon-m-light-bulb',
            'pending', => 'heroicon-m-bolt',
            'processing' => 'heroicon-m-document-magnifying-glass',
            'completed' => 'heroicon-m-rocket-launch',
            'patient_rescheduled', 'doctor_rescheduled' => 'heroicon-m-calendar',
            'problematic', 'patient_late', 'doctor_late' => 'heroicon-m-rocket-launch',
            'doctor_canceled', 'patient_canceled' => 'heroicon-m-calendar'
        };
    }

    public static function processingStage(): array {
        return [self::PROCESSING, self::PATIENT_RESCHEDULED, self::DOCTOR_RESCHEDULED];
    }

    public static function problematicProcessingStage(): array {
        return [self::PATIENT_CANCELED, self::DOCTOR_CANCELED, self::DOCTOR_LATE, self::PATIENT_LATE, self::PROBLEMATIC];
    }

}
