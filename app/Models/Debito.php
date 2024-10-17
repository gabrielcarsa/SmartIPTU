<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debito extends Model
{
    use HasFactory;
    protected $table = 'debito';
    public $timestamps = false;

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function tipoDebito()
    {
        return $this->belongsTo(TipoDebito::class);
    }

    public function parcelaContaPagar()
    {
        return $this->hasMany(ParcelaContaPagar::class);
    }

    public function parcelaContaReceber()
    {
        return $this->hasMany(ParcelaContaReceber::class);
    }
}
