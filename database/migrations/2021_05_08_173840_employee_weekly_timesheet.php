<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmployeeWeeklyTimesheet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_weekly_timesheet', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time_from');
            $table->time('time_to');
            $table->string('hours');
            $table->string('comments');
            $table->json('pay_rate')->default(json_encode([['id' => 1, 'type' => 'normal', 'pay_rate' => 10, 'selected' => true], ['id' => 2, 'type' => 'extra', 'pay_rate' => 15, 'selected' => false]]));
            $table->foreignId('employee_id')->constrained('employees')->onUpdate('cascade')->nullable();
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
        Schema::dropIfExists('employee_weekly_timesheet');
    }
}
