<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;
    protected $table = 'lote';
    public $timestamps = false;

    protected $fillable = [
        'quadra_id',
        'lote',
        'metros_quadrados',
        'valor',
        'endereco',
        'matricula',
        'inscricao_municipal',
        'metragem_frente',
        'metragem_fundo',
        'metragem_direita',
        'metragem_esquerda',
        'metragem_esquina',
        'confrontacao_frente',
        'confrontacao_fundo',
        'confrontacao_direita',
        'confrontacao_esquerda',
        'cliente_id',
        'data_venda',
    ];

    public function quadra()
    {
        return $this->belongsTo(Quadra::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
