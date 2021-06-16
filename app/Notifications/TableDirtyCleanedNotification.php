<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TableDirtyCleanedNotification extends Notification
{
    use Queueable;

    public $message;
    public $table;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message, $table)
    {
        $this->message = $message;
        $this->table = $table;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }
    public function toDatabase($notifiable)
    {
        return [
            'employee_id' => $this->table->employee_id,
            'toDatabase' => 'toDatabase',
            'message' => $this->message,
            'link' => route(
                'tables.index'
            ),
            'table' => $this->table
        ];
    }
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            [
                'employee_id' => $this->table->employee_id,
                'toBroadcast' => 'toBroadcast',
                'message' => $this->message,
                'table' => $this->table,
                // 'created' => \Illuminate\Support\Carbon::parse($this->table->created_at)->diffForHumans(),
                'created' => $this->table->updated_at,
                'read_at' => null,
                'link' => route(
                    'tables.index'
                ),
                'notifiable_id' => $notifiable->id,
                'type' => __NAMESPACE__,
            ]
        ]);
    }
}
