<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::create('kassas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash')->default(0);
            $table->unsignedBigInteger('card')->default(0);
            $table->unsignedBigInteger('bank')->default(0);

            $table->unsignedBigInteger('out_cash_pending')->default(0);
            $table->unsignedBigInteger('out_card_pending')->default(0);
            $table->unsignedBigInteger('out_bank_pending')->default(0);

            $table->unsignedBigInteger('cost_cash_pending')->default(0);
            $table->unsignedBigInteger('cost_card_pending')->default(0);
            $table->unsignedBigInteger('cost_bank_pending')->default(0);
            $table->timestamps();
        });
    }
    
    public function down(): void{
        Schema::dropIfExists('kassas');
    }
};
