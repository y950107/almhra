<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewStudentRegistrationAr extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $candidate;

    public function __construct($user, $candidate)
    {
        $this->user = $user;
        $this->candidate = $candidate;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تسجيل طالب جديد - ' . app(\App\Settings\GeneralSettings::class)->branch_name)
            ->greeting('السلام عليكم ورحمة الله وبركاته')
            ->line('تم تسجيل طالب جديد في النظام، وهذه بياناته:')
            ->line('الاسم: ' . $this->user->name)
            ->line('البريد الإلكتروني: ' . $this->user->email)
            ->line('رقم الهاتف: ' . $this->user->phone)
            ->action('مراجعة الطلب', url('/admin/candidates/'))
            ->salutation('وفقكم الله');
    }
}