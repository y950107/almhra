<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CandidateEvaluationNotification extends Notification
{
    use Queueable;

    public string $status;
    public $email;
    public $password;
    /**
     * Create a new notification instance.
     */
    public function __construct(string $status, $email, $password)
    {
        $this->status = $status;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via(object $notifiable): array
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

        if ($this->status === 'accepted') {
            $mailMessage->line('تهانينا! لقد تم قبولك كطالب في مدرسة تحفيظ القرآن.')
                ->line('يمكنك الآن تسجيل الدخول باستخدام البيانات التالية:')
                ->line('📧 البريد الإلكتروني: ' . $this->email)
                ->line('🔑 كلمة المرور: ' . $this->password)
                ->action('تسجيل الدخول', url('/login'))
                ->line('نرجو لك رحلة مباركة في حفظ كتاب الله.');
        } else {
            $mailMessage->line('نأسف، لم تحقق نسبة النجاح المطلوبة وتم وضعك في قائمة الاحتياط.')
                ->line('نتمنى لك التوفيق، ويمكنك المحاولة مرة أخرى في المستقبل.');
        }

        return $mailMessage->salutation('مع تحيات فريق إدارة المدرسة');
    }
}
