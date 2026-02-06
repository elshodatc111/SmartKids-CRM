<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('tkun')->default(false);
            $table->boolean('password_update')->default(false);
            $table->boolean('emploes_paymart')->default(false);
            $table->boolean('visited')->default(false);
            $table->boolean('payment')->default(false);
            $table->boolean('debit')->default(false);
            $table->boolean('davomad')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void{
        Schema::dropIfExists('settings');
    }
};
