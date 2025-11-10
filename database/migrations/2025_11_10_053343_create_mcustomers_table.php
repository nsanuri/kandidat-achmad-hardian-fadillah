<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMcustomersTable extends Migration
{
    public function up()
    {
        Schema::create('mcustomers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('password')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mcustomers');
    }
}
