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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->restrictOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('property_type');
            $table->string('address_line');
            $table->string('city');
            $table->string('region');
            $table->string('country')->default('Namibia');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index('city');
        });

        // Unique name per organization, but only among non-deleted rows,
        // so a soft-deleted property doesn't block reusing its name.
        DB::statement(
            'CREATE UNIQUE INDEX properties_org_name_unique
         ON properties (organization_id, lower(name))
         WHERE deleted_at IS NULL'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
