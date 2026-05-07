<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Bootstrap the full schema from a single SQL file.
     */
    public function up(): void
    {
        $schemaFile = database_path('exports/nexoralabs_tech.sql');

        if (!is_file($schemaFile)) {
            throw new RuntimeException("Schema file not found: {$schemaFile}");
        }

        $sql = file_get_contents($schemaFile);
        if ($sql === false) {
            throw new RuntimeException("Unable to read schema file: {$schemaFile}");
        }

        DB::unprepared($sql);
    }

    /**
     * Drop all tables in the current connection database.
     */
    public function down(): void
    {
        $databaseName = DB::getDatabaseName();

        $tables = DB::table('information_schema.tables')
            ->where('table_schema', $databaseName)
            ->pluck('table_name')
            ->all();

        if (empty($tables)) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            DB::statement("DROP TABLE IF EXISTS `{$table}`");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
