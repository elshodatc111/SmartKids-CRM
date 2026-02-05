<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kid_id');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->integer('amount');
            $table->enum('payment_type',['cash', 'card', 'bank','discount','return']);
            $table->enum('status',['success','pedding','cancel'])->default('pedding');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('kassir_user_id');
            $table->unsignedBigInteger('success_admin_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('payments');
    }
};
