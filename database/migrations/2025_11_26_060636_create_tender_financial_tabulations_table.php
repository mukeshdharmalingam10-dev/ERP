<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderFinancialTabulationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_financial_tabulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->onDelete('cascade');
            $table->string('pl_number')->nullable();
            $table->date('bid_closed_date')->nullable();
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
        Schema::dropIfExists('tender_financial_tabulations');
    }
}
