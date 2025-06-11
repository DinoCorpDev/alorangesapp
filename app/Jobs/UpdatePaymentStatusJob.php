<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\CombinedOrder;
use App\Models\Order;
use App\Http\Services\WompiServices;

class UpdatePaymentStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $orders = Order::where('payment_status', 'unpaid')->get();

        foreach ($orders as $order) {
            if ($order->combined_order_id) {
                $combinedOrder = CombinedOrder::find($order->combined_order_id);

                if (!$combinedOrder) {
                    \Log::warning("No se encontró CombinedOrder con ID {$order->combined_order_id}");
                    continue;
                }

                $code = $combinedOrder->code;
                $wompiResult = (new WompiServices)->wompiGetTransactionFacturas($code);

                // Aquí puedes hacer parsing del estado real
                $paymentStatus = $wompiResult ?? 'unknown';

                Order::where('combined_order_id', $combinedOrder->id)
                    ->update(['payment_status' => $paymentStatus]);

                \Log::info("Se actualizó el estado de pago a '{$paymentStatus}' para órdenes del CombinedOrder ID {$combinedOrder->id}");                
            }
        }
    }
}
