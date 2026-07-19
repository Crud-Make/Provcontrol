<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fechamento_frentistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fechamento_id')->constrained()->onDelete('cascade');
            $table->foreignId('frentista_id')->constrained()->onDelete('cascade');
            $table->decimal('valor_encerrante', 15, 2)->nullable();
            $table->decimal('valor_cartao_debito', 15, 2)->default(0);
            $table->decimal('valor_cartao_credito', 15, 2)->default(0);
            $table->decimal('valor_cartao', 15, 2)->default(0);
            $table->decimal('valor_pix', 15, 2)->default(0);
            $table->decimal('valor_dinheiro', 15, 2)->default(0);
            $table->decimal('valor_nota', 15, 2)->default(0);
            $table->decimal('valor_moedas', 15, 2)->default(0);
            $table->decimal('valor_baratao', 15, 2)->default(0);
            $table->decimal('valor_produtos', 15, 2)->default(0);
            $table->decimal('total_informado', 15, 2)->default(0);
            $table->decimal('valor_conferido', 15, 2)->default(0);
            $table->decimal('falta_caixa', 15, 2)->default(0);
            $table->decimal('diferenca', 15, 2)->default(0);
            $table->string('status', 20)->default('pendente');
            $table->text('observacoes')->nullable();
            $table->foreignId('enviado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('enviado_em')->nullable();
            $table->timestamps();
            $table->unique(['fechamento_id', 'frentista_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fechamento_frentistas');
    }
};
