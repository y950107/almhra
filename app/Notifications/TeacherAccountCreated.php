<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TeacherAccountCreated extends Notification implements ShouldQueue
{
    use Queueable;
    protected string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('حساب جديد في نظام المدرسة')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم إنشاء حسابك بنجاح:')
            ->line('اسم المستخدم: ' . $notifiable->email)
            ->line('كلمة المرور: ' . $this->password)
            ->action('تسجيل الدخول', url('/login'))
            ->line('يمكنك تغيير كلمة المرور بعد الدخول.');
    }
}