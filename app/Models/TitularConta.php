<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitularConta extends Model
{
    use HasFactory;
    protected $table = 'titular_conta';
    public $timestamps = false;

    public function cliente(){
        return $this->belongsTo(Cliente::Class);
    }

    public function movimentacao_financeira()
    {
        return $this->hasMany(MovimentacaoFinanceira::class);
    }
}
