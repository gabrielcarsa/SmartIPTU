<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $table = 'cliente';
    public $timestamps = false;

    public function lote()
    {
        return $this->hasMany(Lote::class);
    }

    public function titular_conta()
    {
        return $this->hasOne(TitularConta::class);
    }

    public function movimentacao_financeira()
    {
        return $this->hasMany(MovimentacaoFinanceira::class);
    }
}
