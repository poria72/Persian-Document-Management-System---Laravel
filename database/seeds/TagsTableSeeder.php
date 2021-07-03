<?php

use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tags')->insert([
            'name' => 'جاری',
            'color' => '#008000',
            'created_by' => 1,
        ]);

        DB::table('tags')->insert([
            'name' => 'راکد',
            'color' => '#ff0000',
            'created_by' => 1,
        ]);

        DB::table('permissions')->insert([
            'name' => 'read documents in tag 1',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'create documents in tag 1',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'update documents in tag 1',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'delete documents in tag 1',
            'guard_name' => 'web',
        ]);
        DB::table('permissions')->insert([
            'name' => 'verify documents in tag 1',
            'guard_name' => 'web',
        ]);
    }
}
