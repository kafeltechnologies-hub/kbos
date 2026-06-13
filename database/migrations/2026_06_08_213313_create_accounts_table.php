<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {

                $table->id();

                $table->string('account_code')->unique();

                $table->string('account_name');

                $table->string('account_type');

                $table->string('account_group');

                $table->foreignId('parent_id')
                    ->nullable()
                    ->constrained('accounts')
                    ->nullOnDelete();

                $table->boolean('active')->default(true);

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
