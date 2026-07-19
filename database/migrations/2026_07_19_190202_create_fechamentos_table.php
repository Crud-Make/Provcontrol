<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fechamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posto_id')->constrained()->onDelete('cascade');
            $table->foreignId('turno_id')->constrained()->onDelete('cascade');
            $table->date('data');
            $table->string('status', 20)->default('aberto');
            $table->decimal('total_vendas_bombas', 15, 2)->default(0);
            $table->decimal('total_recebido', 15, 2)->default(0);
            $table->decimal('diferenca', 15, 2)->default(0);
            $table->text('observacoes')->nullable();
            $table->foreignId('fechado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fechado_em')->nullable();
            $table->timestamps();
            $table->unique(['posto_id', 'data', 'turno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fechamentos');
    }
};
