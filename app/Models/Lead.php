<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'amocrm_id', 'name', 'price', 'responsible_user_id', 'status_id', 'loss_reason_id',
        'pipeline_id', 'created_by', 'updated_by', 'closed_at', 'closest_task_at',
        'is_deleted', 'custom_fields_values', 'score', 'account_id', 'source_id',
        'is_price_modified_by_robot'
    ];
}
