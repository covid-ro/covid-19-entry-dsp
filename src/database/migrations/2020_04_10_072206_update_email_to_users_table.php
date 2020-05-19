<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmailToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('users', 'email_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('email_verified_at');
            });
        }


        Schema::table('users', function (Blueprint $table) {
            $schemaManager = Schema::connection('mysql')->getConnection()->getDoctrineSchemaManager();;
            $usersTable = $schemaManager->listTableDetails('users');

            if ($usersTable->hasIndex('users_email_unique')) {
                $table->dropIndex('users_email_unique');
            }

            $table->string('email')->unique(false)->change();
            $table->string('email')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique(true)->change();
            $table->string('email')->nullable(false)->change();
            $table->timestamp('email_verified_at')->nullable();
        });
    }
}
