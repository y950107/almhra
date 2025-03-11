<?php

namespace App\Enums;

enum EvaluationStatus: string
{
    case PENDING = 'pending';
    case PASSED = 'passed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('status.pending'),
            self::PASSED => __('status.passed'),
            self::FAILED => __('status.failed'),
        };
    }
}
