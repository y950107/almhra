<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CandidateAccepted extends Notification implements ShouldQueue
{
    use Queueable;
   
    protected $password;
    /**
     * Create a new notification instance.
     */
    public function __construct($password)
    {
        
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return(new MailMessage)
            ->subject('نتيجة تقييم المقابلة')
            ->greeting('السلام عليكم ' . $notifiable->full_name . '،')
            ->line('تهانينا! لقد تم قبولك كطالب في مدرسة تحفيظ القرآن.')
            ->line('يمكنك الآن تسجيل الدخول باستخدام البيانات التالية:')
            ->line('📧 البريد الإلكتروني: ' . $notifiable->email)
            ->line('🔑 كلمة المرور: ' . $this->password)
            ->action('تسجيل الدخول', url('/student/login'))
            ->line('نرجو لك رحلة مباركة في حفظ كتاب الله.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
