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
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('debtor_id')->constrained('users')->cascadeOnDelete(); // ลูกหนี้
            $table->foreignId('creditor_id')->constrained('users')->cascadeOnDelete(); // เจ้าหนี้
            $table->decimal('amount', 10, 2);
            $table->dateTime('transfer_datetime')->nullable();
            $table->string('slip_image', 255)->nullable();
            $table->string('status', 45)->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
