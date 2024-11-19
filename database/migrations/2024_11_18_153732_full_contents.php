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
        Schema::connection($this->getConnectionName())->table($this->getTableName(), function (Blueprint $table) {
            $table->string('title')->after('updated_at')->nullable();
            $table->text('description')->after('title')->nullable();
            $table->longText('content')->after('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->getConnectionName())->table($this->getTableName(), function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'content']);
        });
    }

    private function getConnectionName(): string
    {
        return config('content-markdown.database.connection', 'sqlite');
    }

    private function getTableName(): string
    {
        return config('content-markdown.database.content_table_name', 'contents');
    }
};
