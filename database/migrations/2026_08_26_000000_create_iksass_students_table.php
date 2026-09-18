<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iksass_students', function (Blueprint $table) {
            $table->foreignId('region_iksass_id')->constrained('regions')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->primary(['region_iksass_id', 'student_id']);
            $table->timestamps();
        });

        $students = DB::table('students')
            ->whereNotNull('region_iksass_id')
            ->select('region_iksass_id', 'id as student_id')
            ->get();

        $insertData = $students->map(function ($item) {
            return [
                'region_iksass_id' => $item->region_iksass_id,
                'student_id' => $item->student_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        DB::table('iksass_students')->insert($insertData);

        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('region_iksass_id');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('region_iksass_id')->nullable()->after('location_type')->constrained('regions')->nullOnDelete();
        });

        Schema::dropIfExists('iksass_students');
    }
};
