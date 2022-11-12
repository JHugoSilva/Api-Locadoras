<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locacao extends Model
{
    use HasFactory;

    protected $table = 'locacoes';

    protected $fillable = [
        'cliente_id',
        'carro_id',
        'data_inicio_periodo',
        'data_final_previsto_periodo',
        'data_final_realizado_periodo',
        'valor_diaria',
        'km_inicial',
        'km_final'
    ];

    public function rules()
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'carro_id' => 'required|exists:carros,id',
            'data_inicio_periodo' => 'required|date_format:Y-m-d H:i:s',
            'data_final_previsto_periodo' => 'required|date_format:Y-m-d H:i:s',
            'data_final_realizado_periodo' => 'required|date_format:Y-m-d H:i:s',
            'valor_diaria' => 'required|numeric|min:2',
            'km_inicial' => 'required|numeric|min:2',
            'km_final' => 'required|numeric|min:2'
        ];
    }

    public function feedback()
    {
        return [

        ];
    }

    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
