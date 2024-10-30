<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaReceber extends Model
{
    use HasFactory;
    protected $table = 'conta_receber';
    public $timestamps = false;

    public function parcela_conta_receber(){
        return $this->hasMany(ParcelaContaReceber::class);
    }

    public function categoria_receber(){
        return $this->belongsTo(CategoriaReceber::class);
    }
}
