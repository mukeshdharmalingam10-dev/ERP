<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_production_lists', function (Blueprint $table) {
            $table->id();
            $table->string('dpl_no')->unique();
            $table->unsignedBigInteger('production_order_id');
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->date('latest_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_production_lists');
    }
};

