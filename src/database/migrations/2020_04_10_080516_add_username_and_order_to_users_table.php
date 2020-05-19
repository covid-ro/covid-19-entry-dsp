<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsernameAndOrderToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id');
            $table->string('import_order_number')->nullable()->after('checkpoint');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('username');
            });
        }

        if (Schema::hasColumn('users', 'import_order_number')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('import_order_number');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $schemaManager = Schema::connection('mysql')->getConnection()->getDoctrineSchemaManager();;
            $usersTable = $schemaManager->listTableDetails('users');

            if ($usersTable->hasIndex('users_username_unique')) {
                $table->dropIndex('users_username_unique');
            }
        });
    }
}
