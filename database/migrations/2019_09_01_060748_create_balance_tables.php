<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * CreateBalanceTables creates tables for balance accounting.
 *
 * @see \Illuminatech\Balance\BalanceDb
 */
class CreateBalanceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('balance')->default(0);
            $table->string('type')->index();
            $table->unsignedInteger('contact_id');

            $table->foreign('contact_id')
                ->references('id')
                ->on('contacts')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        Schema::create('balance_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('extra_account_id')->nullable();
            $table->integer('amount');
            $table->json('data')->nullable();
            $table->timestamp('created_at');

            $table->foreign('account_id')
                ->references('id')
                ->on('balance_accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade')
            ;

            $table->foreign('extra_account_id')
                ->references('id')
                ->on('balance_accounts')
                ->onUpdate('cascade')
                ->onDelete('cascade')
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balance_transactions');
        Schema::dropIfExists('balance_accounts');
    }
}
