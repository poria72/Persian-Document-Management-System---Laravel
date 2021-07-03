<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Setting::create(['name'=>'system_title','value'=>'مدیریت اسناد']);
        \App\Setting::create(['name'=>'system_logo','value'=>'logo.png']);

        \App\Setting::create(['name'=>'tags_label_singular','value'=>'برچسب']);
        \App\Setting::create(['name'=>'tags_label_plural','value'=>'برچسب ها']);

        \App\Setting::create(['name'=>'document_label_singular','value'=>'پرونده']);
        \App\Setting::create(['name'=>'document_label_plural','value'=>'پرونده ها']);

        \App\Setting::create(['name'=>'file_label_singular','value'=>'فایل']);
        \App\Setting::create(['name'=>'file_label_plural','value'=>'فایل ها']);

        \App\Setting::create(['name'=>'default_file_validations','value'=>'mimes:jpeg,bmp,png,jpg']);
        \App\Setting::create(['name'=>'default_file_maxsize','value'=>'8']);

        \App\Setting::create(['name'=>'image_files_resize','value'=>'300,500,700']);

        \App\Setting::create(['name'=>'show_missing_files_errors','value'=>'true']);

        \App\Setting::create(['name'=>'home_label_singular','value'=>'خانه']);

        \App\Setting::create(['name'=>'custom_fields_label_singular','value'=>'فیلدهای دلخواه']);

        \App\Setting::create(['name'=>'advanced_settings_label_singular','value'=>'تنظیمات پیشرفته']);
        \App\Setting::create(['name'=>'settings_label_singular','value'=>'تنظیمات']);

        \App\Setting::create(['name'=>'search_label_singular','value'=>'جست و جو']);

        \App\Setting::create(['name'=>'user_label_singular','value'=>'کاربر']);
        \App\Setting::create(['name'=>'user_label_plural','value'=>'کاربران']);

}
}
