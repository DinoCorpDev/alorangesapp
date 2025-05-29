<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\CombinedOrder;
use App\Http\Services\WompiServices;

class ConsultarEstadoPagoWompi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $combinedOrderId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $combinedOrderId)
    {
        $this->combinedOrderId = $combinedOrderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $combinedOrder = CombinedOrder::with('orders')->find($this->combinedOrderId);

        if (!$combinedOrder) {
            // Podrías loguear este caso si es raro
            return;
        }

        $wompiResult = (new WompiServices)->wompiGetTransactionFacturas($combinedOrder->code);
        foreach ($combinedOrder->orders as $order) {
            $order->update([
                'payment_status' => $wompiResult,
            ]);
        }
    }
}
