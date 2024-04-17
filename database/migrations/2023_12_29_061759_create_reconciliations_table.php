<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReconciliationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reconciliations', function (Blueprint $table) {
            $table->id();
            $table->integer('recon_status')->default(0)->comment = "0-not yet reconciled, 1-done reconcile, 2-for removal, 3-request for edit";
            $table->smallInteger('final_recon_status')->default(0)->comment = "0-Not, 1-Yes";
            $table->string('po_date')->nullable();
            $table->string('po_num')->nullable();
            $table->string('pr_num')->nullable();
            $table->string('prod_code')->nullable();
            $table->string('prod_name')->nullable();
            $table->string('prod_desc')->nullable();
            $table->string('supplier')->nullable();
            $table->string('currency')->nullable();
            $table->string('uom')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('received_qty')->nullable();
            $table->string('po_balance')->nullable();
            $table->string('pic')->nullable();
            $table->string('received_date')->nullable();
            $table->string('delivery_date')->nullable();
            $table->string('received_by')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('rcv_no')->nullable();
            $table->string('classification')->nullable();
            $table->string('allocation')->nullable();
            $table->longText('po_remarks')->nullable();
            $table->longText('hold_remarks')->nullable();
            $table->string('recon_invoice_no')->nullable();
            $table->string('recon_received_qty')->nullable();
            $table->string('recon_amount')->nullable();
            $table->string('recon_date_from')->nullable();
            $table->string('recon_date_to')->nullable();
            $table->string('recon_remove_remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reconciliations');
    }
}
