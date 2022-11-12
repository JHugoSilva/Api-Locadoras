<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;

class ClienteController extends Controller
{

    private $cliente;

    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clienteRepository = new ClienteRepository($this->cliente);

        if ($request->has('atributos') && !empty($request->atributos)) {
            $clienteRepository->selectAtributos($request->atributos);
        }

        if ($request->has('filtro') && !empty($request->filtro)) {
            $clienteRepository->filtro($request->filtro);
        }

        $cliente = $clienteRepository->get();

        if ($cliente === null) {
            return response()->json(['msg' => 'Cliente não encontradas'], 404);
        }

        return response()->json($cliente, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->cliente->rules(), $this->cliente->feedback());

        $cliente = $this->cliente->create([
            'nome' => $request->nome,
        ]);

        return response()->json($cliente, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $cliente = $this->cliente->find($id);
        if ($cliente === null) {
            return response()->json(['msg' => 'Cliente não localizada'], 404);
        }
        return response()->json($cliente, 200);
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
        $cliente = $this->cliente->find($id);

        if ($cliente === null) {
            return response()->json(['msg' => 'Cliente não atualizada'], 404);
        }

        if ($request->method() === 'PATCH') {
            $regras_dinamicas = [];
            foreach ($cliente->roles() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regras_dinamicas[$input] = $regra;
                }
            }
            $request->validate($regras_dinamicas, $cliente->feedback());
        } else {
            $request->validate($cliente->rules(), $cliente->feedback());
        }

        $cliente = $cliente->fill($request->all());
        $cliente->save();
        return response()->json('Cliente atualizada com sucesso', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cliente = $this->cliente->find($id);
        if ($cliente === null) {
            return response()->json(['msg' => 'Cliente não localizada'], 404);
        }

        $cliente = $cliente->delete();
        return response()->json('Cliente apagada com sucesso', 200);
    }
}
