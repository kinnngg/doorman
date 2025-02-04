<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('doorman.invite_table_name'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('for')->nullable()->unique();
            $table->integer('max')->default(1);
            $table->integer('uses')->default(0);
            $table->timestamp('valid_until')->nullable();

            $table->unsignedInteger('user_to_id')->nullable();
            $table->unsignedInteger('user_use_id')->nullable();

            $table->string('payment_mode')->nullable();
            $table->string('payment_txnid')->nullable();
            $table->float('payment_amount')->nullable();
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
        Schema::dropIfExists(config('doorman.invite_table_name'));
    }
}
