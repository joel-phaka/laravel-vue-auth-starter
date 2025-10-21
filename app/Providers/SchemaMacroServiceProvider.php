<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SchemaMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Schema::macro('dropColumnsIfExist', function (string $table, array|string $columns) {
            $columns = Arr::wrap($columns);
            $tableColumns = Schema::getColumnListing($table);
            $columnsToDrop = collect($columns)->filter(fn ($column) => in_array($column, $tableColumns))->toArray();

            if (!empty($columnsToDrop)) {
                Schema::dropColumns($table, $columnsToDrop);;
            }
        });
    }
}
