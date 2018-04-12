<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment = "task owner user id";
            $table->string('name');
            $table->string('command');
            $table->unsignedTinyInteger('trigger')->comment = "0: none; 1: timer; 2: interval; 3: api";
            $table->string('schedule')->nullable();
            $table->unsignedInteger('server_id')->nullable();
            $table->unsignedInteger('cluster_id')->nullable();
            $table->unsignedInteger('cluster_policy')
                ->comment = "1: first pong; 2: polling; 3: random; 4: consistent hash; 5: broadcast; 6: fragment broadcast";
            $table->unsignedInteger('failure_policy')
                ->comment = "0: ignore; 1: retry; 2: failover; 4: notify task owner (email); 8: notify task owner (cellphone)";
            $table->unsignedTinyInteger('overlap_policy')->comment = "1: overlap; 2: serial; 3: kill";
            $table->string('trigger_tasks')->nullable()->comment = "trigger these tasks if succeed";
            $table->timestamp('disabled_at');
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
        Schema::dropIfExists('tasks');
    }
}
