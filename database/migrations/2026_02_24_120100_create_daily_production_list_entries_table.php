<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_production_list_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dpl_id')->constrained('daily_production_lists')->onDelete('cascade');
            $table->integer('set_no')->nullable();
            $table->integer('sub_set_no')->nullable();
            $table->string('item_name');
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('completed_qty', 15, 2)->nullable();
            $table->date('entry_date');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_production_list_entries');
    }
};

