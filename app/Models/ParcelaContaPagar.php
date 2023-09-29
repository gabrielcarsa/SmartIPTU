<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParcelaContaPagar extends Model
{
    use HasFactory;
    protected $table = 'parcela_conta_pagar';
    public $timestamps = false;
}
