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
}
