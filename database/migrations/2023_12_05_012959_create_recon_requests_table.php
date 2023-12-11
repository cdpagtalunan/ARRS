<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReconRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recon_requests', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('status')->default(0)->comment="0-pending approval, 1-approve, 2-disapprove";
            $table->smallInteger('request_type')->default(0)->comment="0-add,1-remove";
            $table->integer('recon_fkid')->nullable()->comment="For add only. fkid of reconciliations";
            $table->string('ctrl_num')->nullable();
            $table->string('ctrl_num_ext')->nullable();
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
            $table->softDeletes();
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
        Schema::dropIfExists('recon_requests');
    }
}
