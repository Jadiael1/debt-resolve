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
            $table->decimal('value', 10, 2);
            $table->integer('installment_number');
            $table->date('due_date');
            $table->boolean('paid')->default(false);

            $table->unsignedBigInteger('charge_id');
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
        });
        Schema::dropIfExists('installments');
    }
};
