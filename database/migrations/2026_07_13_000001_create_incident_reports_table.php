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
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->enum('incident_type', [
                'near_miss',
                'unsafe_act',
                'unsafe_condition',
                'kecelakaan_ringan',
                'kecelakaan_berat',
                'kebakaran',
                'tumpahan_kimia',
                'lainnya'
            ]);
            $table->string('location');
            $table->enum('urgency', ['rendah', 'sedang', 'tinggi', 'kritis']);
            $table->text('description');
            $table->date('incident_date');
            $table->time('incident_time')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('reporter_name')->nullable();
            $table->string('reporter_department')->nullable();
            $table->string('reporter_phone')->nullable();
            $table->enum('status', ['baru', 'ditinjau', 'dalam_penanganan', 'selesai', 'ditolak'])->default('baru');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
