<?php

use App\Models\Account;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_pipelines', function (Blueprint $table) {
            $table->id();
            $table->integer('amocrm_id')->unique();
            $table->string('name');
            $table->boolean('is_main');
            $table->boolean('is_archive');
            $table->foreignIdFor(Account::class, 'account_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_pipelines');
    }
};
