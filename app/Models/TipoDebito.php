<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDebito extends Model
{
    use HasFactory;
    protected $table = 'tipo_debito';
    public $timestamps = false;

    public function debito()
    {
        return $this->hasMany(Debito::class);
    }
}
