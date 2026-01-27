<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('status_id')->constrained()->onDelete('restrict');
            $table->foreignId('severity_id')->constrained()->onDelete('restrict');
            $table->foreignId('developer_id')->constrained('users')->onDelete('set null');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
