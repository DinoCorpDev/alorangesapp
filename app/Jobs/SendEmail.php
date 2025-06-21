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
use App\Models\CombinedOrder;
use App\Notifications\OrderPlacedNotification;
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
                if (!$combinedOrder) {
                    \Log::warning("CombinedOrder no encontrado para el ID {$order->combined_order_id}");
                    continue;
                }
                try {
                    //$user = User::find($combinedOrder->user_id);
                    $emailTest = 'brayantriana22@gmail.com';
                    $emailUser = (new AnonymousNotifiable)->route('mail', $emailTest);
                    Notification::send([$emailUser, $adminEmail],new OrderPlacedNotification($combinedOrder));

                    Order::where('combined_order_id', $combinedOrder->id)
                        ->update(['email_send' => 1]);
                } catch (\Exception $e) {
                    \Log::error("Fallo al enviar notificación: " . $e->getMessage());
                }
            }else{
                \Log::warning("Error en envio de correo");
            }
        }
    }
}
