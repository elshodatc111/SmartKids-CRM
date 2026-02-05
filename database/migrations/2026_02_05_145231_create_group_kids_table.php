<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::create('group_kids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('kids_id');
            $table->enum('status', ['active','delete'])->default('active');
            $table->timestamp('add_data');
            $table->unsignedBigInteger('add_admin_id');
            $table->timestamp('delete_data')->nullable();
            $table->unsignedBigInteger('delete_admin_id')->nullable();
            $table->string('payment_month');
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('group_kids');
    }
};
