<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    private $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/reset-password/' . $this->token);

        return (new MailMessage)
                    ->subject('パスワード通知のリセット')
                    ->greeting('こんにちは！')
                    ->line('アカウントのパスワードリセットリクエストを受け取ったため、このメールを受信して​​います。')
                    ->action('パスワードを再設定する', $url)
                    ->line('このパスワードリセットリンクは60分で期限切れになります。')
                    ->line('パスワードのリセットを要求しなかった場合、それ以上のアクションは必要ありません。');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
