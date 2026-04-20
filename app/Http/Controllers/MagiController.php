<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

/**
 * MagiController — Dynamic multi-DB analytics via .env connections.
 *
 * Add to routes/web.php:
 *   Route::get('/db/tables',         [MagiController::class, 'listTables']);
 *   Route::post('/db/fetch-table',   [MagiController::class, 'fetchTable']);
 */
class MagiController extends Controller
{

    /**
     * List all available DB connections defined in .env / config/database.php
     * and return their tables with FK relations.
     */
    public function listTables(Request $request): JsonResponse
    {
        $connectionName = $request->query('connection', config('database.default'));

        try {
            $driver = config("database.connections.{$connectionName}.driver");
            if (!$driver) {
                return response()->json(['success' => false, 'message' => "Connection '{$connectionName}' not found in config."], 422);
            }

            $db     = DB::connection($connectionName);
            $tables = $this->getTableList($db, $driver, $connectionName);
            $fks    = $this->getForeignKeys($db, $driver, $connectionName);

            // Build adjacency: table → [related tables via FK]
            $relations = [];
            foreach ($fks as $fk) {
                $relations[$fk['table']][]            = $fk['referenced_table'];
                $relations[$fk['referenced_table']][] = $fk['table'];
            }

            // Available named connections from config
            $availableConnections = array_keys(config('database.connections', []));

            return response()->json([
                'success'     => true,
                'connection'  => $connectionName,
                'driver'      => $driver,
                'tables'      => $tables,
                'foreign_keys'=> $fks,
                'relations'   => array_map('array_unique', $relations),
                'connections' => $availableConnections,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch a table (with its FK-joined relatives) and return
     * normalised rows + headers ready for the JS dashboard engine.
     */
    public function fetchTable(Request $request): JsonResponse
{
    $request->validate([
        'table'      => 'required|string|max:128',
        'connection' => 'nullable|string|max:64',
    ]);

    $connectionName = $request->input('connection', config('database.default'));
    $baseTable      = $request->input('table');

    try {
        $driver = config("database.connections.{$connectionName}.driver");
        if (!$driver) {
            return response()->json(['success' => false, 'message' => "Connection '{$connectionName}' not found."], 422);
        }

        $db  = DB::connection($connectionName);
        $fks = $this->getForeignKeys($db, $driver, $connectionName);

        // Find FKs where the base table is the owning side
        $joins = array_filter($fks, fn($fk) => $fk['table'] === $baseTable);

        // Build query with LEFT JOINs on related tables
        $query        = $db->table($baseTable);
        $baseColumns = $this->getColumns($db, $driver, $baseTable, $connectionName);
$selectCols  = array_map(fn($c) => "{$baseTable}.{$c}", $baseColumns);
        $joinedTables = [];

        foreach ($joins as $fk) {
    $ref = $fk['referenced_table'];
    if (in_array($ref, $joinedTables)) continue;
    $joinedTables[] = $ref;

    $refCols = $this->getColumns($db, $driver, $ref, $connectionName);

    // Find the best "name" column in the referenced table
    $nameCol = null;
    $namePriority = ['name', 'title', 'label', 'full_name', 'fullname',
                     'display_name', 'product_name', 'first_name', 'description'];
    foreach ($namePriority as $kw) {
        foreach ($refCols as $col) {
            if (strtolower($col) === $kw || str_contains(strtolower($col), $kw)) {
                $nameCol = $col;
                break 2;
            }
        }
    }

    foreach ($refCols as $col) {
        $colLow = strtolower($col);

        // Skip PK of joined table
        if ($col === $fk['referenced_column']) continue;

        $isMeaningful = false;
        $meaningfulKw = ['name', 'title', 'label', 'mobile', 'phone', 'email',
                         'first', 'last', 'full', 'display', 'description',
                         'city', 'address', 'status', 'type', 'code', 'number',
                         'product', 'item', 'category', 'brand', 'model',
                         'lead', 'client', 'customer', 'contact', 'company',
                         'user', 'agent', 'assigned', 'owner'];
        foreach ($meaningfulKw as $kw) {
            if (str_contains($colLow, $kw)) {
                $isMeaningful = true;
                break;
            }
        }
        if (!$isMeaningful) continue;

        // Use the FK column name as alias if this is the primary name col
        // so e.g. "productname" shows the actual product name, not the ID
        $alias = ($col === $nameCol)
            ? $fk['column']          // overwrite the FK id column with the name
            : $ref . '__' . $col;

        $selectCols[] = "{$ref}.{$col} as {$alias}";
    }

    // Remove the raw FK id column from base table select if we resolved its name
    if ($nameCol) {
        $selectCols = array_filter($selectCols, fn($c) =>
            $c !== "{$baseTable}.{$fk['column']}" &&
            $c !== "{$baseTable}.*"  // will re-add exclusion below
        );
    }

    $query->leftJoin(
        $ref,
        "{$baseTable}.{$fk['column']}",
        '=',
        "{$ref}.{$fk['referenced_column']}"
    );
}

        $query->select($selectCols);
        $rawRows = $query->get();

        if ($rawRows->isEmpty()) {
            return response()->json(['success' => false, 'message' => "Table '{$baseTable}' is empty."], 422);
        }

        $rowsArray   = $rawRows->map(fn($r) => (array)$r)->toArray();
        $headers     = array_keys($rowsArray[0]);
        $indexedRows = array_map(fn($row) => array_values($row), $rowsArray);

        $sheetsData = [[
            'sheet'   => $baseTable,
            'index'   => 0,
            'headers' => $headers,
            'rows'    => $indexedRows,
            'count'   => count($indexedRows),
            'cols'    => count($headers),
            'meta'    => [
                'source'     => 'database',
                'connection' => $connectionName,
                'driver'     => $driver,
                'table'      => $baseTable,
                'joined'     => $joinedTables,
                'total_rows' => count($indexedRows),
                'total_cols' => count($headers),
            ],
        ]];

        return response()->json([
            'success'    => true,
            'sheets'     => $sheetsData,
            'sheetCount' => 1,
            'filename'   => $baseTable,
        ]);

    } catch (\Exception $e) {
        \Log::error('fetchTable error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    /* ── Private helpers ─────────────────────────────────────── */

    private function getTableList($db, string $driver, string $connection): array
    {
        $dbName = config("database.connections.{$connection}.database");

        return match ($driver) {
            'mysql', 'mariadb' => array_map(
                fn($r) => (array)$r,
                $db->select("
                    SELECT TABLE_NAME as name,
                           TABLE_ROWS as row_estimate,
                           TABLE_COMMENT as comment
                    FROM information_schema.TABLES
                    WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'
                    ORDER BY TABLE_NAME
                ", [$dbName])
            ),
            'pgsql' => array_map(
                fn($r) => (array)$r,
                $db->select("
                    SELECT tablename as name,
                           0 as row_estimate,
                           '' as comment
                    FROM pg_catalog.pg_tables
                    WHERE schemaname = 'public'
                    ORDER BY tablename
                ")
            ),
            'sqlsrv' => array_map(
                fn($r) => (array)$r,
                $db->select("
                    SELECT TABLE_NAME as name,
                           0 as row_estimate,
                           '' as comment
                    FROM INFORMATION_SCHEMA.TABLES
                    WHERE TABLE_TYPE = 'BASE TABLE'
                    ORDER BY TABLE_NAME
                ")
            ),
            default => [],
        };
    }

    private function getForeignKeys($db, string $driver, string $connection): array
    {
        $dbName = config("database.connections.{$connection}.database");

        $rows = match ($driver) {
            'mysql', 'mariadb' => $db->select("
                SELECT
                    kcu.TABLE_NAME        AS `table`,
                    kcu.COLUMN_NAME       AS `column`,
                    kcu.REFERENCED_TABLE_NAME  AS referenced_table,
                    kcu.REFERENCED_COLUMN_NAME AS referenced_column
                FROM information_schema.KEY_COLUMN_USAGE kcu
                JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
                    ON rc.CONSTRAINT_NAME   = kcu.CONSTRAINT_NAME
                    AND rc.CONSTRAINT_SCHEMA = kcu.TABLE_SCHEMA
                WHERE kcu.TABLE_SCHEMA = ?
                  AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
            ", [$dbName]),

            'pgsql' => $db->select("
                SELECT
                    tc.table_name             AS table,
                    kcu.column_name           AS column,
                    ccu.table_name            AS referenced_table,
                    ccu.column_name           AS referenced_column
                FROM information_schema.table_constraints AS tc
                JOIN information_schema.key_column_usage AS kcu
                    ON tc.constraint_name = kcu.constraint_name
                    AND tc.table_schema   = kcu.table_schema
                JOIN information_schema.constraint_column_usage AS ccu
                    ON ccu.constraint_name = tc.constraint_name
                    AND ccu.table_schema   = tc.table_schema
                WHERE tc.constraint_type = 'FOREIGN KEY'
                  AND tc.table_schema    = 'public'
            "),

            'sqlsrv' => $db->select("
                SELECT
                    tp.name AS [table],
                    cp.name AS [column],
                    tr.name AS referenced_table,
                    cr.name AS referenced_column
                FROM sys.foreign_key_columns fkc
                JOIN sys.tables  tp ON fkc.parent_object_id      = tp.object_id
                JOIN sys.columns cp ON fkc.parent_object_id      = cp.object_id AND fkc.parent_column_id      = cp.column_id
                JOIN sys.tables  tr ON fkc.referenced_object_id  = tr.object_id
                JOIN sys.columns cr ON fkc.referenced_object_id  = cr.object_id AND fkc.referenced_column_id  = cr.column_id
            "),

            default => [],
        };

        return array_map(fn($r) => (array)$r, $rows);
    }

    private function getColumns($db, string $driver, string $table, string $connection): array
    {
        $dbName = config("database.connections.{$connection}.database");

        $rows = match ($driver) {
            'mysql', 'mariadb' => $db->select("
                SELECT COLUMN_NAME as col
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
                ORDER BY ORDINAL_POSITION
            ", [$dbName, $table]),
            'pgsql' => $db->select("
                SELECT column_name as col
                FROM information_schema.columns
                WHERE table_schema = 'public' AND table_name = ?
                ORDER BY ordinal_position
            ", [$table]),
            'sqlsrv' => $db->select("
                SELECT COLUMN_NAME as col
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = ?
                ORDER BY ORDINAL_POSITION
            ", [$table]),
            default => [],
        };

        return array_column(array_map(fn($r) => (array)$r, $rows), 'col');
    }
}