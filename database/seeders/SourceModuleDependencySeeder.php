<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SourceModuleDependencySeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SourceProductsSeeder::class,
            SourceBrokerMasterSeeder::class,
        ]);
    }
}
