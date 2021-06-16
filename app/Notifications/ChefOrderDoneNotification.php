<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChefOrderDoneNotification extends Notification
{
    use Queueable;

    public $message;
    public $order;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message, Order $order)
    {
        $this->order = $order;
        $this->message = $message;
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
            'employee_id' => $this->order->waiter_id,
            'message' => $this->message,
            'link' => route(
                'tables.index'
            ),
            'order' => $this->order
        ];
    }
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            [
                'employee_id' => $this->order->waiter_id,
                'message' => $this->message,
                'order' => $this->order,
                // 'created' => \Illuminate\Support\Carbon::parse($this->order->created_at)->diffForHumans(),
                'created' => $this->order->updated_at,
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
