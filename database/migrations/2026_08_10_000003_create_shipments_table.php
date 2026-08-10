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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('region_iksass_id')->constrained('regions');
            $table->string('sender_name');
            $table->string('sender_phone')->nullable();
            $table->decimal('amount_money', 15, 2)->default(0);
            $table->unsignedInteger('package_count')->default(1);
            $table->enum('status', ['created', 'in_transit', 'delivered'])->default('created');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('region_iksass_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
