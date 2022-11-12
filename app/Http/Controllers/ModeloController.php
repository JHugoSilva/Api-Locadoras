<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{

    private $modelo;

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);
        if ($request->has('atributos_marca') && !empty($request->atributos_marca)) {
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        } else {
            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if ($request->has('atributos') && !empty($request->atributos)) {
            $modeloRepository->selectAtributos($request->atributos);
        }

        if ($request->has('filtro') && !empty($request->filtro)) {
            $modeloRepository->filtro($request->filtro);
        }

        $modelos =  $modeloRepository->get();
        return response()->json($modelos, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules(), $this->modelo->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $modelo = $this->modelo->with('marca')->find($id);
        if ($modelo === null) {
            return response()->json(['msg' => 'Modelo não localizado'], 404);
        }
        return $modelo;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $modelo = $this->modelo->find($id);
        if ($modelo === null) {
            return response()->json(['msg' => 'Modelo não localizado'], 404);
        }


        if ($request->method() === 'PATCH') {
            $regras_dinamicas = [];

            foreach ($modelo->rules() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regras_dinamicas[$input] = $regra;
                }
            }

            $request->validate($regras_dinamicas, $modelo->feedback());
        } else {
            $request->validate($modelo->rules(), $modelo->feedback());
        }

        if ($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
            $image = $request->file('imagem');
            $imagem_urn = $image->store('imagens/modelos', 'public');
        } else {
            $imagem_urn =  $modelo->imagem;
        }

        $modelo = $modelo->fill($request->all());
        $modelo->imagem =  $imagem_urn;
        $modelo->save();
        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $modelo = $this->modelo->find($id);
        if ($modelo === null) {
            return response()->json(['msg' => 'Modelo não localizada'], 404);
        }

        Storage::disk('public')->delete($modelo->imagem);

        $modelo = $modelo->delete();
        return response()->json('Marca apagada com sucesso', 200);
    }
}
