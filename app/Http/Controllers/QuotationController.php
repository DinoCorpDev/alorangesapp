<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = trim((string) $request->get('busqueda'));

        $cotizaciones = Quotation::query()
            ->when($busqueda !== '', function ($q) use ($busqueda) {
                $q->where(function ($sub) use ($busqueda) {
                    $sub->where('customer_name', 'LIKE', "%{$busqueda}%")
                        ->orWhere('customer_document', 'LIKE', "%{$busqueda}%")
                        ->orWhere('number', 'LIKE', "%{$busqueda}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->appends(['busqueda' => $busqueda]);

        return view('backend.quotations.index', compact('cotizaciones', 'busqueda'));
    }

    public function create()
    {
        $cotizacion = new Quotation([
            'number' => Quotation::siguienteNumero(),
            'issue_date' => now(),
            'expiry_date' => now()->addDays(15),
        ]);

        return view('backend.quotations.form', [
            'cotizacion' => $cotizacion,
            'lineas' => [],
            'modo' => 'crear',
        ]);
    }

    public function edit($id)
    {
        $cotizacion = Quotation::with('items')->findOrFail($id);

        return view('backend.quotations.form', [
            'cotizacion' => $cotizacion,
            'lineas' => $cotizacion->items,
            'modo' => 'editar',
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        $cotizacion = DB::transaction(function () use ($datos, $request) {
            $cotizacion = new Quotation();
            $cotizacion->number = Quotation::siguienteNumero();
            $this->rellenar($cotizacion, $datos);
            $cotizacion->created_by = auth()->id();
            $cotizacion->created_by_name = $this->nombreComercial();
            $cotizacion->save();

            $this->guardarLineas($cotizacion, $request->input('lineas', []));
            $cotizacion->load('items');
            $cotizacion->recalcularTotales();

            return $cotizacion;
        });

        flash(translate('Cotización creada correctamente'))->success();

        return redirect()->route('quotations.edit', $cotizacion->id);
    }

    public function update(Request $request, $id)
    {
        $datos = $this->validar($request);
        $cotizacion = Quotation::findOrFail($id);

        DB::transaction(function () use ($cotizacion, $datos, $request) {
            $this->rellenar($cotizacion, $datos);
            $cotizacion->save();

            // Se reemplazan todas las lineas: es mas simple y fiable que
            // intentar casar cuales cambiaron, y el volumen es pequeno.
            $cotizacion->items()->delete();
            $this->guardarLineas($cotizacion, $request->input('lineas', []));

            $cotizacion->load('items');
            $cotizacion->recalcularTotales();
        });

        flash(translate('Cotización actualizada'))->success();

        return redirect()->route('quotations.edit', $cotizacion->id);
    }

    public function destroy($id)
    {
        $cotizacion = Quotation::findOrFail($id);
        $cotizacion->items()->delete();
        $cotizacion->delete();

        flash(translate('Cotización eliminada'))->success();

        return redirect()->route('quotations.index');
    }

    /**
     * Genera el PDF con el mismo formato que las cotizaciones actuales.
     */
    public function pdf($id)
    {
        $cotizacion = Quotation::with('items')->findOrFail($id);

        $html = view('backend.quotations.pdf', [
            'cotizacion' => $cotizacion,
            'empresa' => $this->datosEmpresa(),
        ])->render();

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 12,
            'tempDir' => storage_path('app/mpdf_temp'),
            'default_font' => 'dejavusans',
        ]);

        $mpdf->SetTitle('Cotizacion-' . $cotizacion->number);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Cotizacion-' . $cotizacion->number . '.pdf"',
        ]);
    }

    /**
     * Datos del emisor. Se leen de ajustes para poder cambiarlos sin tocar
     * codigo, con los valores reales de Aloranges como respaldo (los ajustes
     * de factura del sistema traen datos de ejemplo).
     */
    protected function datosEmpresa(): array
    {
        return [
            'nombre' => get_setting('quotation_company_name') ?: 'ALORANGES S.A.S.',
            'nit' => get_setting('quotation_company_nit') ?: 'NIT 901810257 -1',
            'telefono' => get_setting('quotation_company_phone') ?: '+573174420109',
            'web' => get_setting('quotation_company_web') ?: 'www.aloranges.com',
            'email' => get_setting('quotation_company_email') ?: 'contabilidad@aloranges.com',
            'direccion' => get_setting('quotation_company_address') ?: 'carrera 66a # 2b 26 - Cali',
            // Logo de la marca desde el fichero del proyecto. No se usa
            // `invoice_logo` porque ese ajuste apunta a una imagen de
            // producto (papel higienico), no al logo.
            'logo' => public_path('assets/img/aloranges-logo.png'),
        ];
    }

    /**
     * Autocompletado de productos del formulario.
     * Devuelve lo justo para pintar la sugerencia y rellenar la linea.
     */
    public function buscarProductos(Request $request)
    {
        $termino = trim((string) $request->get('q'));

        if (mb_strlen($termino) < 2) {
            return response()->json(['productos' => []]);
        }

        $productos = Product::query()
            ->where(function ($q) use ($termino) {
                $q->where('name', 'LIKE', "%{$termino}%")
                  ->orWhere('reference', 'LIKE', "%{$termino}%");
            })
            // Misma relevancia que el buscador de la tienda: primero lo que
            // empieza por el termino y lo que tiene precio, porque hay 4.109
            // productos a 0 que si no coparian las sugerencias.
            ->orderByRaw('CASE WHEN reference = ? THEN 0 ELSE 1 END', [$termino])
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', [$termino . '%'])
            ->orderByRaw('CASE WHEN lowest_price > 0 THEN 0 ELSE 1 END')
            ->orderBy('name')
            ->limit(12)
            ->get(['id', 'reference', 'name', 'lowest_price', 'tax', 'unit', 'thumbnail_img']);

        return response()->json([
            'productos' => $productos->map(function ($p) {
                return [
                    'id' => $p->id,
                    'referencia' => (string) ($p->reference ?? ''),
                    'nombre' => $p->name,
                    'precio' => (float) $p->lowest_price,
                    'impuesto' => (float) ($p->tax ?: 0),
                    'unidad' => $p->unit,
                    'imagen' => $p->thumbnail_img ? uploaded_asset($p->thumbnail_img) : null,
                ];
            })->values(),
        ]);
    }

    /**
     * Nombre del comercial para el "Elaborado por" del PDF.
     * El modelo User no tiene un campo `name`: guarda nombres y apellidos
     * por separado.
     */
    protected function nombreComercial(): ?string
    {
        $u = auth()->user();
        if (!$u) {
            return null;
        }

        $partes = array_filter([
            $u->first_name ?? null,
            $u->second_name ?? null,
            $u->first_lastname ?? null,
            $u->second_lastname ?? null,
        ]);

        $nombre = trim(implode(' ', $partes));

        return $nombre !== '' ? $nombre : ($u->email ?? null);
    }

    protected function validar(Request $request): array
    {
        return $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_document' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string',
            'customer_phone' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'observations' => 'nullable|string',
            'lineas' => 'required|array|min:1',
            'lineas.*.name' => 'required|string|max:255',
            'lineas.*.image_path' => 'nullable|string|max:500',
            'lineas.*.unit_price' => 'required|numeric|min:0',
            'lineas.*.quantity' => 'required|numeric|min:0.01',
            'lineas.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'lineas.*.tax_percent' => 'nullable|numeric|min:0|max:100',
        ], [
            'lineas.required' => translate('Agrega al menos un producto a la cotización'),
        ]);
    }

    protected function rellenar(Quotation $cotizacion, array $datos): void
    {
        $cotizacion->fill([
            'customer_name' => $datos['customer_name'],
            'customer_document' => $datos['customer_document'] ?? null,
            'customer_address' => $datos['customer_address'] ?? null,
            'customer_phone' => $datos['customer_phone'] ?? null,
            'customer_email' => $datos['customer_email'] ?? null,
            'issue_date' => $datos['issue_date'],
            'expiry_date' => $datos['expiry_date'] ?? null,
            'observations' => $datos['observations'] ?? null,
        ]);
    }

    protected function guardarLineas(Quotation $cotizacion, array $lineas): void
    {
        $posicion = 0;

        foreach ($lineas as $fila) {
            $linea = new QuotationItem([
                'quotation_id' => $cotizacion->id,
                'product_id' => $fila['product_id'] ?? null,
                'reference' => $fila['reference'] ?? null,
                'name' => $fila['name'],
                'image_path' => $fila['image_path'] ?? null,
                'unit_price' => (float) $fila['unit_price'],
                'quantity' => (float) $fila['quantity'],
                'discount_percent' => (float) ($fila['discount_percent'] ?? 0),
                'tax_percent' => (float) ($fila['tax_percent'] ?? 0),
                'position' => $posicion++,
            ]);

            $linea->calcularImportes();
            $linea->save();
        }
    }
}
