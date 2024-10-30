<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaPagar extends Model
{
    use HasFactory;
    protected $table = 'conta_pagar';
    public $timestamps = false;

    public function parcela_conta_pagar(){
        return $this->hasMany(ParcelaContaPagar::class);
    }

    public function categoria_pagar(){
        return $this->belongsTo(CategoriaPagar::class);
    }
}