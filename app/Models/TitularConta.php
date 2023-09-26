<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitularDebito extends Model
{
    use HasFactory;
    protected $table = 'titular_conta';
    public $timestamps = false;
}
