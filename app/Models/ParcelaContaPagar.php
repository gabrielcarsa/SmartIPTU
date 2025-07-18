<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParcelaContaPagar extends Model
{
    use HasFactory;
    protected $table = 'parcela_conta_pagar';
    public $timestamps = false;

    public function debito()
    {
        return $this->belongsTo(Debito::class);
    }

    public function movimentacao_financeira()
    {
        return $this->belongsTo(MovimentacaoFinanceira::class);
    }

    public function conta_pagar()
    {
        return $this->belongsTo(ContaPagar::class);
    }

    public function descricao_debito()
    {
        return $this->belongsTo(DescricaoDebito::class);
    }
}
