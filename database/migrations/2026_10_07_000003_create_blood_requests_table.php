<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('blood_group', 5)->index();
            $table->unsignedTinyInteger('units_needed');
            $table->unsignedTinyInteger('units_fulfilled')->default(0);
            $table->string('hospital');
            $table->string('contact_name');
            $table->string('contact_phone', 20);
            $table->date('needed_by')->nullable();
            $table->enum('urgency', ['routine', 'urgent', 'critical'])->default('routine');
            $table->enum('status', ['pending', 'fulfilled', 'cancelled'])->default('pending')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
