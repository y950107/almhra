<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentAccountCreated extends Notification implements ShouldQueue
{
    use Queueable;


    public $email;
    public $password;
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail']; 
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('قبولك كطالب في مدرسة تحفيظ القرآن')
            ->greeting('السلام عليكم ورحمة الله وبركاته')
            ->line('تهانينا! لقد تم قبولك كطالب في مدرسة تحفيظ القرآن.')
            ->line('يمكنك الآن تسجيل الدخول باستخدام البيانات التالية:')
            ->line('📧 البريد الإلكتروني: ' . $this->email)
            ->line('🔑 كلمة المرور: ' . $this->password)
            ->action('تسجيل الدخول', url('/login'))
            ->line('نرجو لك رحلة مباركة في حفظ كتاب الله.');
    }
}