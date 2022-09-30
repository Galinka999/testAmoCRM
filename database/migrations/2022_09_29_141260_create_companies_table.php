<?php

use App\Models\Account;
use App\Models\ResponsibleUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->integer('amocrm_id')->unique();
            $table->string('name');
            $table->foreignIdFor(ResponsibleUser::class, 'responsible_user_id');
            $table->foreignIdFor(Account::class, 'account_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
