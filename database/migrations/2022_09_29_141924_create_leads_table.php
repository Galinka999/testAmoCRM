<?php

use App\Models\LeadPipeline;
use App\Models\LeadStatus;
use App\Models\LossReason;
use App\Models\ResponsibleUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->integer('amocrm_id')->index();
            $table->string('name');
            $table->integer('price');
            $table->foreignIdFor(ResponsibleUser::class, 'responsible_user_id')->index();
            $table->foreignIdFor(LeadStatus::class,'status_id')->index();
            $table->foreignIdFor(LossReason::class, 'loss_reason_id')->index();
            $table->foreignIdFor(LeadPipeline::class, 'pipeline_id')->index();
            $table->foreignIdFor(ResponsibleUser::class, 'created_by')->index();
            $table->foreignIdFor(ResponsibleUser::class, 'updated_by')->index();
            $table->timestamps();
            $table->timestamp('closed_at');
            $table->timestamp('closest_task_at');
            $table->boolean('is_deleted');
            $table->json('custom_fields_values')->nullable();
            $table->integer('score')->nullable();
            $table->integer('account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
