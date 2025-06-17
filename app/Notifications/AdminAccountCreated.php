<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAccountCreated extends Notification implements ShouldQueue
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
            ->subject('حساب جديد في نظام مدرسة المهرة')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم إنشاء حسابك بنجاح:')
            ->line('اسم المستخدم: ' . $notifiable->email)
            ->line('كلمة المرور: ' . $this->password)
            ->action('تسجيل الدخول', url('/admin/login'))
            ->line('يمكنك تغيير كلمة المرور بعد الدخول.');
    }
}
