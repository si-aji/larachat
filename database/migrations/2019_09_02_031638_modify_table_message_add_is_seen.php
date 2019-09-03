<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTableMessageAddIsSeen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('message', 'is_seen')){
            Schema::table('message', function (Blueprint $table) {
                $table->boolean('is_seen')->default(false)->after('message');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasColumn('message', 'is_seen')){
            Schema::table('message', function (Blueprint $table) {
                $table->dropColumn('is_seen');
            });
        }
    }
}
