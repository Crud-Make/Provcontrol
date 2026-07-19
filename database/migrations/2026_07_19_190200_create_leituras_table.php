<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leituras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posto_id')->constrained()->onDelete('cascade');
            $table->foreignId('bico_id')->constrained()->onDelete('cascade');
            $table->foreignId('turno_id')->nullable()->constrained()->nullOnDelete();
            $table->date('data');
            $table->decimal('leitura_inicial', 15, 3);
            $table->decimal('leitura_final', 15, 3);
            $table->decimal('preco_litro', 10, 4);
            $table->decimal('litros_vendidos', 15, 3);
            $table->decimal('valor_total', 15, 2);
            $table->string('status', 20)->default('rascunho');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->unique(['bico_id', 'data', 'turno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leituras');
    }
};
