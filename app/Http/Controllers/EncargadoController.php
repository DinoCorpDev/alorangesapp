<?php

namespace App\Http\Controllers;

use App\Http\Requests\EncargadoRequest;
use App\Models\Encargado;

class EncargadoController extends Controller
{
    public function store(EncargadoRequest $request)
    {
        Encargado::updateOrCreate([
            'nombre_encargado' => $request->nombre_encargado,
            'indicativo' => $request->indicativo,
            'encargado_telefono' => $request->encargado_telefono,
        ]);

        return response()->json([
            'success' => true,
            'message' => translate('Encargado was successfully created'),
        ], 200);
    }

    public function update(EncargadoRequest $request, $id)
    {
        Encargado::where('id', $id)->update([
            'nombre_encargado' => $request->nombre_encargado,
            'indicativo' => $request->indicativo,
            'encargado_telefono' => $request->encargado_telefono
        ]);
        return response()->json([
            'success' => true,
            'message' => translate('Encargado was successfully updated'),
        ], 200);
    }

    public function destroy($id)
    {
        Encargado::where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => translate('Encargado was successfully removed')
        ], 200);
    }
}
