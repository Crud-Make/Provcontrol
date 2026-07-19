<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frentistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posto_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nome', 100);
            $table->string('cpf', 14)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->date('data_admissao')->nullable();
            $table->string('foto_url', 255)->nullable();
            $table->boolean('ativo')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frentistas');
    }
};
