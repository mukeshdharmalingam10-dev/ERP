<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->string('sales_type', 50)->default('Tender'); // Tender, Enquiry
            $table->foreignId('customer_order_id')->nullable()->constrained('customer_orders')->onDelete('set null');
            $table->foreignId('proforma_invoice_id')->nullable()->constrained('proforma_invoices')->onDelete('set null');
            $table->string('production_order_no')->nullable();
            $table->string('customer_po_no')->nullable();
            $table->string('work_order_no')->unique();
            $table->string('title')->nullable();
            $table->string('worker_type', 50)->nullable(); // Employee, Sub-Contractor
            $table->unsignedBigInteger('worker_id')->nullable(); // employee_id or supplier_id
            $table->string('product_name')->nullable();
            $table->string('quantity_type', 50)->nullable(); // Sets, Sub Sets, Nos, Others
            $table->decimal('no_of_sets', 15, 2)->nullable();
            $table->integer('starting_set_no')->nullable();
            $table->integer('ending_set_no')->nullable();
            $table->decimal('no_of_sub_sets_per_set', 15, 2)->nullable();
            $table->decimal('total_sub_sets', 15, 2)->nullable();
            $table->decimal('quantity_per_set', 15, 2)->nullable();
            $table->decimal('no_of_quantity', 15, 2)->nullable();
            $table->integer('starting_quantity_no')->nullable();
            $table->integer('ending_quantity_no')->nullable();
            $table->string('thickness')->nullable();
            $table->string('drawing_no')->nullable();
            $table->string('color')->nullable();
            $table->date('completion_date')->nullable();
            $table->string('nature_of_work')->nullable();
            $table->string('layup_sequence')->nullable();
            $table->string('batch_no')->nullable();
            $table->date('work_order_date')->nullable();
            $table->string('document_path')->nullable();
            $table->text('remarks')->nullable();
            $table->json('reference_table_data')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('existing_work_order_id')->nullable();
            $table->timestamps();
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->foreign('existing_work_order_id')->references('id')->on('work_orders')->onDelete('set null');
        });

        Schema::create('work_order_raw_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('cascade');
            $table->decimal('work_order_quantity', 15, 2)->default(0);
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->integer('sr_no')->default(1);
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
        Schema::dropIfExists('work_order_raw_materials');
        Schema::dropIfExists('work_orders');
    }
};
