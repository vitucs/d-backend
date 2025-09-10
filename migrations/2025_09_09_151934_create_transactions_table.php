<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->foreignId('payer_id')->constrained('users')->onDelete('restrict');
            
            $table->foreignId('payee_id')->constrained('users')->onDelete('restrict');
            
            $table->decimal('amount', 10, 2);
            
            $table->enum('status', ['completed', 'failed'])->default('completed');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
