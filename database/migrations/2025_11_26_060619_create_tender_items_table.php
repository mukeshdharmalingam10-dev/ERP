<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->onDelete('cascade');
            $table->string('pl_code')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('delivery_location')->nullable();
            $table->decimal('qty', 15, 2)->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->enum('request_for_price', ['Yes', 'No'])->default('No');
            $table->decimal('price_received', 15, 2)->nullable();
            $table->decimal('price_quoted', 15, 2)->nullable();
            $table->string('tender_status')->nullable();
            $table->string('bid_result')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_items');
    }
}
