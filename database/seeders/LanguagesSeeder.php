<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $language = array(
            array('code_id' => 40,'name' => 'English','code' => 'en','position' => 'ltr','status' => 1,'is_default'=>1),
        );
        foreach ($language as $key => $value) {
            $check = Language::where('name',$value['name'])->where('code',$value['code'])->first();
            if(!$check){
                Language::insert([
                    'code_id' => $value['code_id'],
                    'name' => $value['name'],
                    'code' => $value['code'],
                    'position' => $value['position'],
                    'status' => $value['status'],
                    'is_default' => $value['is_default'],
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }
        }  
    }
}
