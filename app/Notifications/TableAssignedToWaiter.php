<?php

namespace App\Notifications;

use App\Models\Table;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TableAssignedToWaiter extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;
    protected $table;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message, Table $table)
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

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'employee_id' => $this->table->employee_id,
            'toArray' => 'toArray',
            'table' => $this->table
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'employee_id' => $this->table->employee_id,
            'toDatabase' => 'toDatabase',
            'message' => $this->message,
            'table' => $this->table,
            'link' => route('tables.index'),
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
                'notifiable_id' => $notifiable->id,
                'type' => __NAMESPACE__,
                'link' => route('tables.index'),
            ]
        ]);
    }
}
