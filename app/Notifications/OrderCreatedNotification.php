<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification
{
    use Queueable;

    public $order;
    public $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message, \App\Models\Order $order)
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
            'toDatabase' => 'toDatabase',
            'message' => $this->message,
            'link' => route(
                'chef.orders.index'
            ),
            'table' => $this->order,
            'link' => route('chef.orders.index'),
        ];
    }
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            [
                'employee_id' => $this->order->waiter_id,
                'toBroadcast' => 'toBroadcast',
                'message' => $this->message,
                'order' => $this->order,
                // 'created' => \Illuminate\Support\Carbon::parse($this->order->created_at)->diffForHumans(),
                'created' => $this->order->updated_at,
                'read_at' => null,
                'link' => route(
                    'chef.orders.index'
                ),
                'notifiable_id' => $notifiable->id,
                'type' => __NAMESPACE__,
            ]
        ]);
    }
}
