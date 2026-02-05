<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void    {
        Schema::create('kids_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kids_id');
            $table->enum('type', ['cash_pay', 
                'card_pay', 
                'bank_pay',
                'discount_add',
                'return_card_pay',
                'return_bank_pay',
                'return_cash_pay',
                'group_add','group_pay',
                'group_delte',
                'vizited'])->default('vizited');
            $table->integer('amount')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable(); // FK YO‘Q
            $table->unsignedBigInteger('group_id')->nullable();
            $table->text('description');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('kids_histories');
    }
};
