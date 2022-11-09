<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;


    protected $fillable = [
        'nome',
        'imagem'
    ];

    public function roles()
    {
        $id = $this->id ? $this->id : 0;
        return [
            'nome' => 'required|min:3|max:25|unique:marcas,nome,'.$id,
            'imagem' => 'required|file|mimes:png,jpeg,jpg|max:8192'
        ];
    }

    public function feedback()
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'O campo :attribute já existe',
            'nome.min' => 'O nome deve ter o mínimo 3 caracteres',
            'nome.max' => 'O nome deve ter no máximo 25 caracteres',
            'imagem.mimes' => 'A imagem deve ser do tipo PNG, JPEG|JPG',
            'imagem.max' => 'A imagem deve ter no maxímo 8MB (8192 KB)',
        ];
    }
}
