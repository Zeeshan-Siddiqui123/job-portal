<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

#[Signature('db:import-sqlite {--source= : Path to the original SQLite database}')]
#[Description('Copy existing SQLite records into an empty, migrated MySQL database')]
class ImportSqlite extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sourcePath = $this->option('source') ?: database_path('database.sqlite');

        if (! is_file($sourcePath)) {
            $this->error('SQLite source file does not exist.');

            return self::FAILURE;
        }

        $target = DB::connection();

        if ($target->getDriverName() !== 'mysql') {
            $this->error('Set DB_CONNECTION=mysql before importing.');

            return self::FAILURE;
        }

        config(['database.connections.sqlite_import' => [
            'driver' => 'sqlite',
            'database' => realpath($sourcePath),
            'prefix' => '',
            'foreign_key_constraints' => true,
            'options' => [\PDO::SQLITE_ATTR_OPEN_FLAGS => \SQLITE3_OPEN_READONLY],
        ]]);
        DB::purge('sqlite_import');
        $source = DB::connection('sqlite_import');
        $tables = [
            'users', 'password_reset_tokens', 'sessions', 'cache', 'cache_locks',
            'jobs', 'job_batches', 'failed_jobs', 'categories', 'job_listings',
            'applications', 'notifications',
        ];

        try {
            $sourceTables = $source->getSchemaBuilder()->getTableListing(schemaQualified: false);
            $unknownTables = array_diff($sourceTables, $tables, ['migrations', 'sqlite_sequence']);

            if ($unknownTables !== []) {
                throw new RuntimeException('Unrecognized source tables: '.implode(', ', $unknownTables));
            }

            foreach ($tables as $table) {
                if (! $target->getSchemaBuilder()->hasTable($table)) {
                    throw new RuntimeException('Run php artisan migrate before importing. Missing table: '.$table);
                }

                if ($target->table($table)->exists()) {
                    throw new RuntimeException('Import stopped: target table '.$table.' is not empty. No records were changed.');
                }
            }

            $source->transaction(function () use ($source, $target, $tables, $sourceTables): void {
                $target->transaction(function () use ($source, $target, $tables, $sourceTables): void {
                    foreach ($tables as $table) {
                        if (! in_array($table, $sourceTables, true)) {
                            continue;
                        }

                        $count = 0;

                        foreach ($source->table($table)->cursor() as $row) {
                            $target->table($table)->insert((array) $row);
                            $count++;
                        }

                        if ($target->table($table)->count() !== $count) {
                            throw new RuntimeException('Record count mismatch for '.$table);
                        }
                    }
                });
            });
        } finally {
            DB::purge('sqlite_import');
        }

        $this->info('SQLite records imported successfully. The original SQLite file is unchanged.');

        return self::SUCCESS;
    }
}
