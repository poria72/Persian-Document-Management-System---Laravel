<?php

use Illuminate\Database\Seeder;

class FileTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\FileType::create([
            'name' => 'General',
            'no_of_files' => 1,
            'labels' => 'page1,page2',
            'file_validations' => 'mimes:jpeg,bmp,png,jpg,pdf,xslx,txt',
            'file_maxsize' => 20
        ]);
    }
}
