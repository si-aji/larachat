<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTableMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('message', 'is_deleted')){
            Schema::table('message', function (Blueprint $table) {
                $table->boolean('is_deleted')->default(false)->after('message');
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
        if(Schema::hasColumn('message', 'is_deleted')){
            Schema::table('message', function (Blueprint $table) {
                $table->dropColumn('is_deleted');
            });
        }
    }
}
