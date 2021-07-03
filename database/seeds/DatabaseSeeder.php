<?php

use App\CustomField;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SettingsTableSeeder::class,
            UsersTableSeeder::class,
            PermissionsTableSeeder::class,
            FileTypesSeeder::class,
            CustomFieldsTableSeeder::class,
            TagsTableSeeder::class,
        ]);
    }
}
