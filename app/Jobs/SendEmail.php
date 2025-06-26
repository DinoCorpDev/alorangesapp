<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\CombinedOrder;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\AnonymousNotifiable;
use App\Models\User;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::info("📨 Job SendEmail iniciado.");

        $orders = Order::where('payment_status', 'APPROVED')
            ->where('email_send', 0)
            ->get();

        if ($orders->isEmpty()) {
            Log::info("✅ No hay órdenes pendientes de envío.");
            return;
        }

        foreach ($orders as $order) {
            if (!$order->combined_order_id) {
                Log::warning("⚠️ Orden ID {$order->id} no tiene combined_order_id.");
                continue;
            }

            $combinedOrder = CombinedOrder::find($order->combined_order_id);

            if (!$combinedOrder) {
                Log::warning("❌ CombinedOrder no encontrado para ID {$order->combined_order_id}");
                continue;
            }
            $user = User::find($combinedOrder->user_id);
            $recipients = [
                $user->email,
                'ventasonlinealoranges@gmail.com',
            ];

            $notifiedSuccessfully = true;

            foreach ($recipients as $email) {
                try {
                    Log::info("📤 Enviando notificación a {$email} para CombinedOrder ID {$combinedOrder->id}");

                    (new AnonymousNotifiable)
                        ->route('mail', $email)
                        ->notify(new OrderPlacedNotification($combinedOrder));

                    Log::info("✅ Notificación enviada a {$email}");

                } catch (\Exception $e) {
                    $notifiedSuccessfully = false;
                    Log::error("❌ Error al enviar notificación a {$email}: " . $e->getMessage());
                }
            }

            // Solo marcar como enviado si todos los correos se enviaron correctamente
            if ($notifiedSuccessfully) {
                Order::where('combined_order_id', $combinedOrder->id)
                    ->update(['email_send' => 1]);

                Log::info("📦 Pedido marcado como enviado (CombinedOrder ID {$combinedOrder->id})");
            } else {
                Log::warning("⚠️ Pedido NO marcado como enviado por errores en el envío (CombinedOrder ID {$combinedOrder->id})");
            }
        }
    }
}