<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReconRequestRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recon_request_remarks', function (Blueprint $table) {
            $table->id();	
            $table->string('recon_request_ctrl_num')->comment = "from recon_request table as fkid";
            $table->string('recon_request_ctrl_num_ext')->comment = "from recon_request table as fkid";
            $table->longText('remarks');
            $table->longText('approver_remarks')->nullable();
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
        Schema::dropIfExists('recon_request_remarks');
    }
}
