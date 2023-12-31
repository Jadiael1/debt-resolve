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
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->decimal('value', 10, 2)->comment('Total installment amount'); // valor total da parcela
            $table->integer('installment_number')->comment('Installment number'); // numero da parcela
            $table->date('due_date')->comment('Due date'); // data de vencimento
            $table->decimal('amount_paid', 10, 2)->nullable()->comment('Amount paid in installment'); // Valor pago na parcela
            $table->boolean('paid')->default(false)->comment('Determines whether the installment has been paid or remains to be paid'); // parcela paga
            $table->string('payment_proof')->nullable()->comment('Proof of payment'); //comprovante do pagamento da parcela
            $table->boolean('awaiting_approval')->default(false)->comment('Determines whether the payment is awaiting approval by the collector.');

            $table->unsignedBigInteger('user_id')->nullable()->comment('Payer identification'); // ID do usuário que pagou
            $table->unsignedBigInteger('charge_id')->comment('Charge identification'); //Identificação da cobrança
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropForeign('installments_charge_id_foreign');
            $table->dropForeign('installments_user_id_foreign');
        });
        Schema::dropIfExists('installments');
    }
};
