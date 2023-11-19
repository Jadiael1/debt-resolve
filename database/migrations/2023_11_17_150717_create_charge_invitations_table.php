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
            $table->string('email')->comment('Email invited to be part of the billing');
            $table->string('token')->comment('Billing invitation token');
            // $table->enum('status', ['debtor', 'collector'])->nullable();
            $table->unsignedBigInteger('charge_id')->comment('Charge identification');
            $table->unsignedBigInteger('user_id')->comment('Identification of the user who made the invitation');
            $table->boolean('is_valid')->default(true)->comment('Defines whether the invitation is still valid or not');
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
            $table->dropForeign('charge_invitations_user_id_foreign');
        });
        Schema::dropIfExists('charge_invitations');
    }
};
