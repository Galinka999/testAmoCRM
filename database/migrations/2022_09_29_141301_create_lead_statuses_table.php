<?php

use App\Models\Account;
use App\Models\LeadPipeline;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_statuses', function (Blueprint $table) {
            $table->id();
            $table->integer('amocrm_id')->unique();
            $table->string('name');
            $table->integer('sort');
            $table->foreignIdFor(LeadPipeline::class,'pipeline_id')->index();
            $table->foreignIdFor(Account::class, 'account_id')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_statuses');
    }
};
