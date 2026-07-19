<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combustiveis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posto_id')->constrained()->onDelete('cascade');
            $table->string('nome', 50);
            $table->string('codigo', 10);
            $table->string('cor', 7)->nullable();
            $table->decimal('preco_atual', 10, 4);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->unique(['posto_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combustiveis');
    }
};
