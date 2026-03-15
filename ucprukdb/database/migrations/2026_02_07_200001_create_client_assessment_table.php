<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_assessment', function (Blueprint $table) {
            $table->string('client_basic_assessment_id', 10)->primary();
            $table->string('client_service_id', 10);
            $table->string('client_id', 10);
            $table->text('diagnosis');
            $table->text('equipment_school_use')->nullable();
            $table->text('equipment_workplace_use')->nullable();
            $table->text('equipment_home_use')->nullable();
            $table->text('equipment_neighborhood_use')->nullable();
            $table->text('other_equipment_use_place')->nullable();
            $table->text('transfer')->nullable();
            $table->text('old_equipment_fulfillment')->nullable();
            $table->text('old_equipment_goodness')->nullable();
            $table->text('cushion_safety')->nullable();
            $table->text('old_equipment_comment')->nullable();
            $table->text('pressure_sores_location_existence')->nullable();
            $table->text('sensation_disruption')->nullable();
            $table->text('pressure_sores_experience')->nullable();
            $table->text('pressure_sores_existence')->nullable();
            $table->enum('open_wound', ['ya', 'tidak'])->default('tidak');
            $table->integer('pressure_sores_level')->nullable();
            $table->text('pressure_sores_duration')->nullable();
            $table->text('pressure_sores_cause')->nullable();
            $table->enum('pressure_sores_risk', ['ya', 'tidak'])->default('tidak');
            $table->enum('both_arms', ['ya', 'tidak'])->default('tidak');
            $table->enum('left_arm', ['ya', 'tidak'])->default('tidak');
            $table->enum('right_arm', ['ya', 'tidak'])->default('tidak');
            $table->enum('both_legs', ['ya', 'tidak'])->default('tidak');
            $table->enum('left_leg', ['ya', 'tidak'])->default('tidak');
            $table->enum('right_leg', ['ya', 'tidak'])->default('tidak');
            $table->enum('other_person', ['ya', 'tidak'])->default('tidak');
            $table->text('pedaling_means_comment')->nullable();
            $table->text('overall_posture')->nullable();
            $table->text('pelvic_posture')->nullable();
            $table->text('posture_comment')->nullable();
            $table->decimal('pelvic_width', 10, 2)->nullable();
            $table->decimal('left_upper_limb', 10, 2)->nullable();
            $table->decimal('right_upper_limb', 10, 2)->nullable();
            $table->decimal('left_lower_limb', 10, 2)->nullable();
            $table->decimal('right_lower_limb', 10, 2)->nullable();
            $table->decimal('buttocks_lower_ribs', 10, 2)->nullable();
            $table->decimal('buttocks_scapula', 10, 2)->nullable();
            $table->decimal('seat_width', 10, 2)->nullable();
            $table->decimal('seat_length', 10, 2)->nullable();
            $table->decimal('upper_holder', 10, 2)->nullable();
            $table->decimal('lower_holder', 10, 2)->nullable();
            $table->decimal('lower_seat', 10, 2)->nullable();
            $table->decimal('middle_seat', 10, 2)->nullable();
            $table->enum('kids_wheelchair', ['ya', 'tidak'])->default('tidak');
            $table->text('rr_holder_width')->nullable();
            $table->integer('rr_holder_length')->nullable();
            $table->integer('rr_seat_height')->nullable();
            $table->text('st_holder_width')->nullable();
            $table->text('cushion_type')->nullable();
            $table->integer('sp_holder_width')->nullable();
            $table->integer('sp_holder_length')->nullable();
            $table->integer('sp_seat_height')->nullable();
            $table->enum('tilting', ['ya', 'tidak'])->default('tidak');
            $table->enum('reclining', ['ya', 'tidak'])->default('tidak');
            $table->enum('folding', ['ya', 'tidak'])->default('tidak');
            $table->enum('light', ['ya', 'tidak'])->default('tidak');
            $table->enum('active', ['ya', 'tidak'])->default('tidak');
            $table->enum('stroller', ['ya', 'tidak'])->default('tidak');
            $table->text('sp_description')->nullable();
            $table->text('sp_reason')->nullable();
            $table->string('photo_before')->nullable(); // Path to compressed JPEG
            $table->string('photo_after')->nullable();  // Path to compressed JPEG
            $table->timestamps();

            // Foreign key and indexes
            $table->foreign('client_id')->references('client_id')->on('client_bio')->onDelete('cascade');
            $table->index('client_id');
            $table->index('client_service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_assessment');
    }
};
