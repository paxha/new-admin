<?php

namespace App\Notifications;

use App\Models\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class LogNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $log;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'slack', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line($this->log->title)
                    ->line($this->log->message)
                    ->line($this->log->description);
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
        ->success()
        ->content($this->log->model . ' ' . $this->log->title)
        ->attachment(function ($attachment) {
            $attachment->title($this->log->name, $this->log->url)
                       ->fields([
                            'Target Name' => $this->log->name,
                            'Action' => $this->log->action . ($this->log->action === 'delete' or $this->log->action === 'inactive' ? ' :x:' : ''),
                            'Action At' => $this->log->created_at,
                            'Action By' => $this->log->user->name,
                        ]);
                    });
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'Title' => $this->log->title,
            'Model' => $this->log->model,
            'Url' => $this->log->url,
            'Target Name' => $this->log->name,
            'Action' => $this->log->action,
            'Action At' => $this->log->created_at,
            'Action By' => $this->log->user->name,
        ];
    }
}
