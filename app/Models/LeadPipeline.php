<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadPipeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'amocrm_id', 'name', 'is_main', 'is_archive', 'account_id'
    ];
}
