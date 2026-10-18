<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->cascadeOnDelete();
            $table->date('donation_date');
            $table->unsignedTinyInteger('units')->default(1);
            $table->string('camp')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['donation_date', 'donor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
