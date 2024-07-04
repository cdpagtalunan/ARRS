<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('rapidx_emp_no')->comment = "Link to Rapidx Employee ID";
            $table->string('category_id')->comment = "from user category";
            $table->unsignedSmallInteger('user_type')->comment = "1-admin, 2-user";
            $table->SmallInteger('user_desig')->comment = "1-Factory_1, 2-Factory_3";
            $table->SmallInteger('is_auth')->default(0)->comment = "0-Not, 1-Yes";
            $table->SmallInteger('is_superior')->default(0)->comment = "0-Not, 1-Yes";
            $table->string('created_by');
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('user_accesses');
    }
}
