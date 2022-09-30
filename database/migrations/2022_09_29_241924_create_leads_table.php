<?php

use App\Models\Account;
use App\Models\LeadPipeline;
use App\Models\LeadStatus;
use App\Models\LossReason;
use App\Models\ResponsibleUser;
use App\Models\Source;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->integer('amocrm_id')->unique()->index();
            $table->string('name');
            $table->integer('price');
            $table->foreignIdFor(ResponsibleUser::class, 'responsible_user_id')->index();
            $table->foreignIdFor(LeadStatus::class,'status_id')->index();
            $table->foreignIdFor(LossReason::class, 'loss_reason_id')->index()->nullable();
            $table->foreignIdFor(LeadPipeline::class, 'pipeline_id')->index();
            $table->foreignIdFor(ResponsibleUser::class, 'created_by')->index();
            $table->foreignIdFor(ResponsibleUser::class, 'updated_by')->index()->nullable();
            $table->timestamps();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('closest_task_at')->nullable();
            $table->boolean('is_deleted');
            $table->json('custom_fields_values')->nullable();
            $table->integer('score')->nullable();
            $table->foreignIdFor(Account::class, 'account_id');
            $table->foreignIdFor(Source::class, 'source_id')->nullable();
            $table->boolean('is_price_modified_by_robot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
