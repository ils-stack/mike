<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourceBrokerMasterSeeder extends Seeder
{
    public function run(): void
    {
        $rows = array_map(
            fn (array $row): array => $this->normalizeRow($row),
            $this->decodedRows()
        );

        DB::table('broker_master')->upsert(
            $rows,
            ['id'],
            [
                'broker_name',
                'email',
                'broker_split',
                'broker_code',
                'broker_factor_1',
                'broker_factor_2',
                'broker_factor_3',
                'broker_factor_4',
                'active',
                'created_at',
                'updated_at',
                'type',
                'consultant_id',
                'vetted',
                'assoc_id',
                'remarks',
                'parent_id',
                'percent',
            ]
        );
    }

    private function normalizeRow(array $row): array
    {
        $updatedAt = $this->normalizeTimestamp($row['updated_at'] ?? null, '2000-01-01 00:00:00');
        $row['created_at'] = $this->normalizeTimestamp($row['created_at'] ?? null, $updatedAt);
        $row['updated_at'] = $updatedAt;

        return $row;
    }

    private function normalizeTimestamp(?string $value, string $fallback): string
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '0000-00-00 00:00:00') {
            return $fallback;
        }

        return $value;
    }

    private function decodedRows(): array
    {
        $json = gzdecode(base64_decode(
            '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'
        ));

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}
