<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescricaoDebito extends Model
{
    use HasFactory;
    protected $table = 'descricao_debito';
    public $timestamps = false;
}
