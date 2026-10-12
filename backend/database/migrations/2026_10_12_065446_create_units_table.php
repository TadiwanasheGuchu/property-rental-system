<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->restrictOnDelete();
            $table->string('unit_number');
            $table->string('unit_type');
            $table->unsignedSmallInteger('bedrooms')->nullable();
            $table->decimal('bathrooms', 3, 1)->nullable();
            $table->smallInteger('floor')->nullable();
            $table->decimal('monthly_rent', 12, 2);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->string('status')->default('available');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'status']);
        });

        DB::statement(
            'CREATE UNIQUE INDEX units_property_number_unique
         ON units (property_id, lower(unit_number))
         WHERE deleted_at IS NULL'
        );

        // Money can't be negative. Enforce it in the database, not just in validation.
        DB::statement('ALTER TABLE units ADD CONSTRAINT units_rent_nonnegative CHECK (monthly_rent >= 0 AND deposit >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
