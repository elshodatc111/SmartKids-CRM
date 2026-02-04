<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::create('kids', function (Blueprint $table) {
            $table->id();
            $table->string('full_name'); // Bolaning to'liq ismi
            $table->bigInteger('balance')->default(0);            
            $table->boolean('is_active')->default(false);
            $table->date('birth_date');
            $table->string('document_series')->unique(); 
            $table->string('guardian_name'); 
            $table->string('guardian_phone');
            $table->string('photo_path')->nullable();
            $table->string('document_photo_path')->nullable();
            $table->string('guardian_passport_path')->nullable();
            $table->string('health_certificate_path')->nullable(); 
            $table->text('address')->nullable();
            $table->text('biography')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); 
            $table->timestamps();
            $table->softDeletes(); // Ma'lumotlarni o'chirib yubormaslik, arxivlash uchun
        });
    }
    public function down(): void{
        Schema::dropIfExists('kids');
    }
};
