<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::create('finance_histories', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['cash', 'card', 'bank'])->default('cash');
            $table->enum('reason',['xarajat', 'daromad', 'ish_haqi','kirim','exson'])->default('xarajat');
            $table->bigInteger('amount');
            $table->string('description')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('start_at')->useCurrent();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('end_data')->nullable()->change();
            $table->bigInteger('donation')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void{
        Schema::dropIfExists('finance_histories');
    }
};
