<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posto_id')->constrained('postos')->onDelete('cascade');
            $table->foreignId('bomba_id')->constrained('bombas')->onDelete('cascade');
            $table->foreignId('combustivel_id')->constrained('combustiveis')->onDelete('cascade');
            $table->smallInteger('numero');
            $table->boolean('ativo')->default(true);
            $table->date('ultima_afericao_em')->nullable();
            $table->timestamps();
            
            $table->unique(['bomba_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bicos');
    }
};
