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
        Schema::create('charge_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token');
            // $table->enum('status', ['debtor', 'collector'])->nullable();
            $table->unsignedBigInteger('charge_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_valid')->default(true);
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('charge_invitations', function (Blueprint $table) {
            $table->dropForeign('charge_invitations_charge_id_foreign');
        });
        Schema::dropIfExists('charge_invitations');
    }
};
