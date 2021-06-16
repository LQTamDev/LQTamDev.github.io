<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeEfficientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_efficients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->nullable()->constrained('tables', 'table_id');
            $table->foreignId('employee_id')->nullable()->constrained('employees', 'id');
            $table->foreignId('order_id')->nullable()->constrained('orders', 'id');
            $table->foreignId('menu_ids')->nullable()->constrained('menus', 'food_id');
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
        Schema::dropIfExists('employee_efficients');
    }
}
