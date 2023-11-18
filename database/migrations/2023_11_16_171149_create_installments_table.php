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
            $table->decimal('value', 10, 2); // valor total da parcela
            $table->integer('installment_number'); // numero da parcela
            $table->date('due_date'); // data de vencimento
            $table->decimal('amount_paid', 10, 2)->nullable(); // Valor pago na parcela
            $table->boolean('paid')->default(false); // parcela paga
            $table->string('payment_proof')->nullable(); //comprovante do pagamento da parcela

            $table->unsignedBigInteger('user_id')->nullable(); // ID do usuário que pagou
            $table->unsignedBigInteger('charge_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('charge_id')->references('id')->on('charges')->onDelete('cascade');
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
