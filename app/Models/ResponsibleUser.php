<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponsibleUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'amocrm_id', 'name', 'email'
    ];
}
