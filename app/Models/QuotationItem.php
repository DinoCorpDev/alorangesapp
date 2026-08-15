<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'product_id',
        'reference',
        'name',
        'image_path',
        'unit_price',
        'quantity',
        'discount_percent',
        'tax_percent',
        'line_subtotal',
        'line_tax',
        'line_total',
        'position',
    ];

    protected $casts = [
        'unit_price' => 'float',
        'quantity' => 'float',
        'discount_percent' => 'float',
        'tax_percent' => 'float',
        'line_subtotal' => 'float',
        'line_tax' => 'float',
        'line_total' => 'float',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calcula los importes de la linea a partir de precio, cantidad,
     * descuento e impuesto. Se llama antes de guardar.
     */
    public function calcularImportes(): void
    {
        $bruto = $this->unit_price * $this->quantity;
        $descuento = $bruto * ($this->discount_percent / 100);
        $base = $bruto - $descuento;

        $this->line_subtotal = round($base, 2);
        $this->line_tax = round($base * ($this->tax_percent / 100), 2);
        $this->line_total = round($base + $this->line_tax, 2);
    }
}
