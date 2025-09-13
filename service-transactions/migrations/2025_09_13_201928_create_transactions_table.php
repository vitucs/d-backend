<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payer_id');
            $table->unsignedBigInteger('payee_id');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('completed');
            $table->timestamps();

            $table->index('payer_id');
            $table->index('payee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
}