<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

    protected $fillable = [
        'marca_id',
        'nome',
        'imagem',
        'numero_portas',
        'lugares',
        'air_bag',
        'abs'
    ];

    public function rules()
    {
        $id = $this->id ? $this->id : 0;
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|min:3|unique:modelos,nome,'.$id,
            'imagem' => 'required|file|mimes:png,jpeg,jpg|max:8192',
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean'
        ];
    }

    public function feedback()
    {
        return [

        ];
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
}
