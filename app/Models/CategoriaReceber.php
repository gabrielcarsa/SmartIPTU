<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaReceber extends Model
{
    use HasFactory;
    protected $table = 'categoria_receber';
    public $timestamps = false;

    public function movimentacao_financeira()
    {
        return $this->hasMany(MovimentacaoFinanceira::class);
    }
}
