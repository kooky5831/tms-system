<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdnumberIdtypeRoletypeOnTrainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->text('id_number')->default(NULL)->nullable()->after('salutationId');
            $table->json('id_type')->nullable()->after('id_number');
            $table->json('role_type')->nullable()->after('id_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dropColumn('id_number');
            $table->dropColumn('id_type');
            $table->dropColumn('role_type');
        });
    }
}
