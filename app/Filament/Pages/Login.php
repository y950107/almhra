<?php

namespace App\Filament\Pages;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Facades\Filament;

class Login extends BaseLogin
{
    public function getHeading(): string
    {
        $panel = Filament::getCurrentPanel()->getId();

        return match ($panel) {
            'admin' => 'تسجيل الدخول - لوحة الإدارة',
            'teacher' => 'تسجيل الدخول - المعلمين ',
            'student' => 'تسجيل الدخول - فضاء الطالب',
            default => 'تسجيل الدخول',
        };
    }
}
