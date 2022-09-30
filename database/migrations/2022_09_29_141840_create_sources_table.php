<?php

use App\Models\LeadPipeline;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->integer('amocrm_id')->unique();
            $table->string('name');
            $table->foreignIdFor(LeadPipeline::class, 'pipeline_id')->index();
            $table->boolean('default');
            $table->string('external_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
