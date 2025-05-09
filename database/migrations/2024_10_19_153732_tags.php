<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->getConnectionName())->create($this->getTableName(), function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->getConnectionName())->dropIfExists($this->getTableName());
    }

    private function getConnectionName(): string
    {
        return config('content-markdown.database.connection', 'sqlite');
    }

    private function getTableName(): string
    {
        return config('content-markdown.database.tag_table_name', 'tags');
    }
};
