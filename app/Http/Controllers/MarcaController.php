<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use App\Repositories\MarcaRepository;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{

    private $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $marcaRepository = new MarcaRepository($this->marca);

        if ($request->has('atributos_modelos') && !empty($request->atributos_modelos)) {
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if ($request->has('atributos') && !empty($request->atributos)) {
            $marcaRepository->selectAtributos($request->atributos);
        }

        if ($request->has('filtro') && !empty($request->filtro)) {
            $marcaRepository->filtro($request->filtro);
        }

        $marcas = $marcaRepository->get();

        if ($marcas === null) {
            return response()->json(['msg' => 'Marcas não encontradas'], 404);
        }

        return response()->json($marcas, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->marca->roles(), $this->marca->feedback());

        $image = $request->file('imagem');
        $imagem_urn = $image->store('imagens', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $marca = $this->marca->with('modelos')->find($id);
        if ($marca === null) {
            return response()->json(['msg' => 'Marca não localizada'], 404);
        }
        return response()->json($marca, 200);
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
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['msg' => 'Marca não atualizada'], 404);
        }

        if ($request->method() === 'PATCH') {
            $regras_dinamicas = [];
            foreach ($marca->roles() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regras_dinamicas[$input] = $regra;
                }
            }
            $request->validate($regras_dinamicas, $marca->feedback());
        } else {
            $request->validate($marca->roles(), $marca->feedback());
        }

        if ($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
            $image = $request->file('imagem');
            $imagem_urn = $image->store('imagens/marcas', 'public');
        } else {
            $imagem_urn =  $marca->imagem;
        }

        $marca = $marca->fill($request->all());
        $marca->imagem =  $imagem_urn;
        $marca->save();
        return response()->json('Marca atualizada com sucesso', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $marca = $this->marca->find($id);
        if ($marca === null) {
            return response()->json(['msg' => 'Marca não localizada'], 404);
        }

        Storage::disk('public')->delete($marca->imagem);

        $marca = $marca->delete();
        return response()->json('Marca apagada com sucesso', 200);
    }
}
