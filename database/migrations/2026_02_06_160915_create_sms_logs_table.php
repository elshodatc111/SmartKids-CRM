<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->text('message');
            $table->string('provider')->default('eskiz');
            $table->string('status')->default('pending'); // pending|sent|failed
            $table->string('provider_message_id')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('sms_logs');
    }
};
