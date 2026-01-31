<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
    public function up(): void{
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->integer('salary_amount')->nullable(); // Oylik miqdori
            $table->date('birth')->nullable(); // Tug'ilgan kuni
            $table->string('series')->nullable(); // Pasport seriyasi
            $table->string('image')->nullable(); // Profil rasmi yo'li
            $table->enum('type', ['admin', 'manager', 'tarbiyachi', 'oshpaz', 'hodim'])->default('hodim');
            $table->boolean('is_active')->default(true); // Hisob holati
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });        
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('phone')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
    public function down(): void{
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};