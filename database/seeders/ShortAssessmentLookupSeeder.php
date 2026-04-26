<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShortAssessmentLookupSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'ss_incomes.sql',
            'ss_expenses.sql',
            'ss_als.sql',
            'ss_tax_table.sql',
            'ss_tax_rebate.sql',
        ] as $file) {
            $sql = file_get_contents(database_path('seed-data/' . $file));
            $statements = preg_split('/;\s*[\r\n]+/', $sql);

            foreach ($statements as $statement) {
                $statement = trim($statement);

                if ($statement === '') {
                    continue;
                }

                DB::statement($statement);
            }
        }
    }
}
