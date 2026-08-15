<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'customer_name',
        'customer_document',
        'customer_address',
        'customer_phone',
        'customer_email',
        'issue_date',
        'expiry_date',
        'observations',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'tax_breakdown',
        'created_by',
        'created_by_name',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'tax_breakdown' => 'array',
        'subtotal' => 'float',
        'discount_total' => 'float',
        'tax_total' => 'float',
        'total' => 'float',
    ];

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('position');
    }

    /**
     * Siguiente numero de cotizacion.
     *
     * Se apoya en el maximo existente en lugar de en un contador aparte para
     * que, al importar cotizaciones antiguas (la ultima en papel es la 5819),
     * la numeracion continue sin saltos ni colisiones.
     */
    public static function siguienteNumero(): int
    {
        $maximo = (int) static::withTrashed()->max('number');

        return max($maximo + 1, (int) get_setting('quotation_start_number', 5820));
    }

    /**
     * Recalcula totales a partir de las lineas y los guarda.
     *
     * El calculo vive en el servidor a proposito: el navegador muestra un
     * avance en vivo, pero lo que se persiste y sale en el PDF se recalcula
     * aqui para que no dependa de lo que llegue del formulario.
     */
    public function recalcularTotales(): void
    {
        $subtotal = 0.0;
        $descuentos = 0.0;
        $impuestos = 0.0;
        $porTarifa = [];

        foreach ($this->items as $linea) {
            $bruto = $linea->unit_price * $linea->quantity;
            $descuento = $bruto * ($linea->discount_percent / 100);
            $base = $bruto - $descuento;
            $iva = $base * ($linea->tax_percent / 100);

            $subtotal += $base;
            $descuentos += $descuento;
            $impuestos += $iva;

            if ($linea->tax_percent > 0) {
                $clave = rtrim(rtrim(number_format($linea->tax_percent, 2, '.', ''), '0'), '.');
                $porTarifa[$clave] = round(($porTarifa[$clave] ?? 0) + $iva, 2);
            }
        }

        $this->subtotal = round($subtotal, 2);
        $this->discount_total = round($descuentos, 2);
        $this->tax_total = round($impuestos, 2);
        $this->total = round($subtotal + $impuestos, 2);
        $this->tax_breakdown = $porTarifa;
        $this->save();
    }
}
