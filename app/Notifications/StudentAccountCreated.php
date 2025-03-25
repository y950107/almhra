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
    /**
     * Create a new notification instance.
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject('نتيجة تقييم المقابلة')
            ->greeting('السلام عليكم ' . $notifiable->full_name . '،');

        if ($notifiable->status === 'accepted') {
            $mailMessage->line('تهانينا! لقد تم قبولك كطالب في مدرسة تحفيظ القرآن.')
                ->line('يمكنك الآن تسجيل الدخول باستخدام البيانات التالية:')
                ->line('📧 البريد الإلكتروني: ' . $notifiable->email)
                ->line('🔑 كلمة المرور: ' . $notifiable->password)
                ->action('تسجيل الدخول', url('/student/login'))
                ->line('نرجو لك رحلة مباركة في حفظ كتاب الله.');
        } else {
            $mailMessage->line('نأسف، لم تحقق نسبة النجاح المطلوبة وتم وضعك في قائمة الاحتياط.')
                ->line('نتمنى لك التوفيق، ويمكنك المحاولة مرة أخرى في المستقبل.');
        }

        return $mailMessage->salutation('مع تحيات فريق إدارة المهرة');
    }
}