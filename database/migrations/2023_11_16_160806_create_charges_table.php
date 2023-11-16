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
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); //nome da cobrança
            $table->string('description'); //descrição da cobrança
            $table->decimal('amount', 10, 2, true); // total da divida
            $table->integer('installments_number')->nullable(); // numero de parcelas
            $table->tinyInteger('due_day'); // dia de vencimento
            $table->unsignedBigInteger('collector_id'); // cobrador
            $table->unsignedBigInteger('debtor_id'); // devedor
            $table->foreign('collector_id')->references('id')->on('users');
            $table->foreign('debtor_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropForeign('charges_collector_id_foreign');
            $table->dropForeign('charges_debtor_id_foreign');
        });
        Schema::dropIfExists('charges');
    }
};
