<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class AbstractRepository {

    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function selectAtributosRegistrosRelacionados($atributos)
    {
        $this->model = $this->model->with($atributos);
    }

    public function selectAtributos($atributos)
    {
        $this->model = $this->model->selectRaw($atributos);
    }

    public function filtro($filtro)
    {
        $filtros = explode(';', $filtro);
        foreach ($filtros as $condicao) {
            $c = explode(':', $condicao);
            $this->model = $this->model->where($c[0], $c[1], $c[2]);
        }
    }

    public function get()
    {
        return $this->model->get();
    }

}
