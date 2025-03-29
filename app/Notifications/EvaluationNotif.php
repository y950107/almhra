<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EvaluationNotif extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    protected $candidate;
    public function __construct($candidate)
    {
        $this->candidate = $candidate;
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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('! موعد مقابلة لتقييم حضوري ')
            ->greeting('السلام عليكم ' . $this->candidate->name . '،')
            ->line('يسرنا إبلاغك بأنه بعد مراجعة كافة معلوماتك  انه قد تم برمجة حصة تقييم حضوري  .')
            ->line('و سيتم التواصل معكم على رقم هاتفكم المسجل مسبقا :' . $this->candidate->phone )
            ->line('نتمنى لك رحلة مباركة في حفظ كتاب الله.');
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
