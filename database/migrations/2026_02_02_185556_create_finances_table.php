<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    
    public function up(): void{
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cash')->unsigned()->default(0);
            $table->bigInteger('card')->unsigned()->default(0);
            $table->bigInteger('bank')->unsigned()->default(0);
            $table->unsignedTinyInteger('donation_percent')->default(0);
            $table->bigInteger('donation')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('finances');
    }
};
