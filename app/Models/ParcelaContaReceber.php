<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParcelaContaReceber extends Model
{
    use HasFactory;
    protected $table = 'parcela_conta_receber';
    public $timestamps = false;

    public function debito()
    {
        return $this->belongsTo(Debito::class);
    }

    public function movimentacao_financeira()
    {
        return $this->belongsTo(MovimentacaoFinanceira::class);
    }

    public function conta_receber()
    {
        return $this->belongsTo(ContaReceber::class);
    }

    public function descricao_debito()
    {
        return $this->belongsTo(DescricaoDebito::class);
    }
}
