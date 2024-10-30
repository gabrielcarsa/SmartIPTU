<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimentacaoFinanceira extends Model
{
    use HasFactory;
    protected $table = 'movimentacao_financeira';
    public $timestamps = false;

    public function categoria_pagar()
    {
        return $this->belongsTo(CategoriaPagar::class);
    }

    public function categoria_receber()
    {
        return $this->belongsTo(CategoriaReceber::class);
    }

    public function tipo_debito()
    {
        return $this->belongsTo(TipoDebito::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_fornecedor_id');
    }

    public function parcela_conta_receber()
    {
        return $this->hasOne(ParcelaContaReceber::class);
    }

    public function parcela_conta_pagar()
    {
        return $this->hasOne(ParcelaContaPagar::class);
    }

    public function titular_conta()
    {
        return $this->belongsTo(TitularConta::class, 'titular_conta_id');
    }

    public function conta_corrente()
    {
        return $this->belongsTo(ContaCorrente::class);
    }
}
