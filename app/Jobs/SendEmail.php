<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\User;
use Notification;
use Illuminate\Notifications\AnonymousNotifiable;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $adminEmail = (new AnonymousNotifiable)->route('mail', 'alorangescorporation@gmail.com');
        
        $orders = Order::where('payment_status', 'APPROVED')->where('email_send', 0)->get();
        foreach ($orders as $order) {
            if ($order->combined_order_id) {
                $combinedOrder = CombinedOrder::find($order->combined_order_id);
                $user = User::find($combinedOrder->user_id);
                $emailTest = 'brayantriana22@gmail.com';
                Notification::send([$emailTest, $adminEmail],new OrderPlacedNotification($combinedOrder));

                Order::where('combined_order_id', $combinedOrder->id)
                    ->update(['email_send' => 1]);
            }else{
                \Log::warning("Error en envio de correo");
            }
        }
    }
}
