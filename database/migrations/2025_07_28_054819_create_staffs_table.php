<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_id')->nullable();
            $table->string('department_id')->nullable();
            $table->string('designation_id')->nullable();
            $table->enum('status', ['In-Service', 'Left'])->default('In-Service');
            $table->foreignId('number_id')->nullable()->constrained('numbers')->onDelete('set null');
            $table->string('balance_limit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('staffs');
    }
};
