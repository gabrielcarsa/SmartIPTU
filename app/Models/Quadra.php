<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quadra extends Model
{
    use HasFactory;
    protected $table = 'quadra';
    public $timestamps = false;

    public function empreendimento()
    {
        return $this->belongsTo(Empreendimento::class);
    }

    public function lote()
    {
        return $this->hasMany(Lote::class);
    }
}
