<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoDiario extends Model
{
    use HasFactory;
    protected $table = 'saldo_diario';
    public $timestamps = false;

}
