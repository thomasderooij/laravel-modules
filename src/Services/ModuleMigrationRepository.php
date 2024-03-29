<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Services;

use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Thomasderooij\LaravelModules\ParentCallTrait;

class ModuleMigrationRepository extends DatabaseMigrationRepository
{
    use ParentCallTrait;

    /**
     * Log things to the migrations table. Apply module if required
     *
     * @param string $file
     * @param int $batch
     * @param null $module
     */
    public function log($file, $batch, $module = null): void
    {
        if ($module === null) {
            $this->parentCall("log", [$file, $batch]);
            return;
        }

        $record = ["migration" => $file, "batch" => $batch, "module" => $module];
        $this->table()->insert($record);
    }

    /**
     * @param int $steps
     * @param string|null $module
     * @return array
     */
    public function getMigrations($steps, string $module = null): array
    {
        if ($module === null) {
            return $this->parentCall("getMigrations", [$steps]);
        }

        $query = $this->table()->where([
            ['batch', '>=', '1'],
            ["module", "=", $module]
        ]);

        return $query->orderBy('batch', 'desc')
            ->orderBy('migration', 'desc')
            ->take($steps)->get()->all();
    }

    /**
     * This is here from that one time I thought rolling back a specific module was a good idea.
     *
     * @param string|null $module
     * @return array
     */
    public function getLast(string $module = null): array
    {
        if ($module === null) {
            return $this->parentCall("getLast");
        }

        $query = $this->table()->where([
            ['batch', "=", $this->getLastBatchNumber($module)],
            ["module", "=", $module]
        ]);

        return $query->orderBy('migration', 'desc')->get()->all();
    }

    /**
     * This is here from that one time I thought rolling back a specific module was a good idea.
     *
     * @param string|null $module
     * @return int|mixed
     */
    public function getLastBatchNumber(string $module = null)
    {
        if ($module === null) {
            return $this->parentCall("getLastBatchNumber");
        }

        return $this->table()->where("module", "=", $module)->max('batch');
    }
}
