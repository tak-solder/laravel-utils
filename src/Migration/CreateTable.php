<?php

namespace TakSolder\LaravelUtils\Migration;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

abstract class CreateTable extends Migration
{
    const TABLE = '';
    const CONNECTION = null;


    /**
     * CreateTable constructor.
     * @param Blueprint $table
     */
    abstract protected function create(Blueprint $table);

    public function up()
    {
        $this->connection()->create(static::TABLE, function (Blueprint $table) {
            $this->create($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->connection()->dropIfExists(static::TABLE);
    }

    protected function connection()
    {
        return Schema::connection(static::CONNECTION);
    }
}
