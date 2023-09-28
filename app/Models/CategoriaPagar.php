<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaPagar extends Model
{
    use HasFactory;
    protected $table = 'categoria_pagar';
    public $timestamps = false;
}
