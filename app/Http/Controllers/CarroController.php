<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    private $carro;

    public function __construct(Carro $carro)
    {
        $this->carro = $carro;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $carroRepository = new CarroRepository($this->carro);

        if ($request->has('atributos_modelos') && !empty($request->atributos_modelos)) {
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $carroRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $carroRepository->selectAtributosRegistrosRelacionados('modelo');
        }

        if ($request->has('atributos') && !empty($request->atributos)) {
            $carroRepository->selectAtributos($request->atributos);
        }

        if ($request->has('filtro') && !empty($request->filtro)) {
            $carroRepository->filtro($request->filtro);
        }

        $carro = $carroRepository->get();

        if ($carro === null) {
            return response()->json(['msg' => 'Marcas não encontradas'], 404);
        }

        return response()->json($carro, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules(), $this->carro->feedback());

        $carro = $this->carro->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);

        return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $carro = $this->carro->with('modelos')->find($id);
        if ($carro === null) {
            return response()->json(['msg' => 'Carro não localizada'], 404);
        }
        return response()->json($carro, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $carro = $this->carro->find($id);

        if ($carro === null) {
            return response()->json(['msg' => 'Carro não atualizada'], 404);
        }

        if ($request->method() === 'PATCH') {
            $regras_dinamicas = [];
            foreach ($carro->roles() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regras_dinamicas[$input] = $regra;
                }
            }
            $request->validate($regras_dinamicas, $carro->feedback());
        } else {
            $request->validate($carro->rules(), $carro->feedback());
        }

        $carro = $carro->fill($request->all());
        $carro->save();
        return response()->json('Carro atualizada com sucesso', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $carro = $this->carro->find($id);
        if ($carro === null) {
            return response()->json(['msg' => 'Carro não localizada'], 404);
        }

        $carro = $carro->delete();
        return response()->json('Carro apagada com sucesso', 200);
    }
}
