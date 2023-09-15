<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empreendimento extends Model
{
    use HasFactory;
    protected $table = 'empreendimento';
    public $timestamps = false;
}
