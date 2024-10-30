<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaPagar extends Model
{
    use HasFactory;
    protected $table = 'categoria_pagar';
    public $timestamps = false;

    public function movimentacao_financeira()
    {
        return $this->hasMany(MovimentacaoFinanceira::class);
    }

    public function conta_pagar(){
        return $this->hasMany(ContaPagar::class);
    }

    public function conta_receber(){
        return $this->hasMany(ContaReceber::class);
    }
}
