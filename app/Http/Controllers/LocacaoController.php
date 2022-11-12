<?php

namespace App\Http\Controllers;

use App\Models\Locacao;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{

    private $locacao;

    public function __construct(Locacao $locacao)
    {
        $this->locacao = $locacao;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $locacaoRepository = new LocacaoRepository($this->locacao);

        if ($request->has('atributos') && !empty($request->atributos)) {
            $locacaoRepository->selectAtributos($request->atributos);
        }

        if ($request->has('filtro') && !empty($request->filtro)) {
            $locacaoRepository->filtro($request->filtro);
        }

        $locacao = $locacaoRepository->get();

        if ($locacao === null) {
            return response()->json(['msg' => 'Locação não encontradas'], 404);
        }

        return response()->json($locacao, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->locacao->rules(), $this->locacao->feedback());

        $locacao = $this->locacao->create([
            'cliente_id' => $request->cliente_id,
            'carro_id' => $request->carro_id,
            'data_inicio_periodo' => $request->data_inicio_periodo,
            'data_final_previsto_periodo' => $request->data_final_previsto_periodo,
            'data_final_realizado_periodo' => $request->data_final_realizado_periodo,
            'valor_diaria' => $request->valor_diaria,
            'km_inicial' => $request->km_inicial,
            'km_final' => $request->km_final
        ]);

        return response()->json($locacao, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $locacao = $this->locacao->with(['carro', 'cliente'])->find($id);
        if ($locacao === null) {
            return response()->json(['msg' => 'Locação não localizada'], 404);
        }
        return response()->json($locacao, 200);
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
        $locacao = $this->locacao->find($id);

        if ($locacao === null) {
            return response()->json(['msg' => 'Locação não atualizada'], 404);
        }

        if ($request->method() === 'PATCH') {
            $regras_dinamicas = [];
            foreach ($locacao->roles() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regras_dinamicas[$input] = $regra;
                }
            }
            $request->validate($regras_dinamicas, $locacao->feedback());
        } else {
            $request->validate($locacao->rules(), $locacao->feedback());
        }

        $locacao = $locacao->fill($request->all());
        $locacao->save();
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
        $locacao = $this->locacao->find($id);
        if ($locacao === null) {
            return response()->json(['msg' => 'Locação não localizada'], 404);
        }

        $locacao = $locacao->delete();
        return response()->json('Locação apagada com sucesso', 200);
    }
}
