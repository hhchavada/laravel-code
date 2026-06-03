<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\BlogCategory;
use App\Models\CategoryTranslation;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
       public function run()
    {
    $categoryArr = [
        array(
            'id' => '1',
            'parent_id'=>'0',
            'name' => 'Demo Category 1', 
            'slug' => \Helpers::createSlug('Demo Category 1','category',0,false),
            'color'=> '#000000',
            'order'=> '1',
            'status'=> '1',
            'is_featured'=> '1'
        ),
        array(
            'id' => '2',
            'parent_id'=>'0',
            'name' => 'Demo Category 2', 
            'slug' => \Helpers::createSlug('Demo Category 2','category',0,false),
            'color'=> '#000000',
            'order'=> '1',
            'status'=> '1',
            'is_featured'=> '1'
        ),
        array(
            'id' => '3',
            'parent_id'=>'0',
            'name' => 'Demo Category 3', 
            'slug' => \Helpers::createSlug('Demo Category 3','category',0,false),
            'color'=> '#000000',
            'order'=> '1',
            'status'=> '1',
            'is_featured'=> '1'
        )
    ];
    $i = 0;
    foreach ($categoryArr as $row) {
        // Check if the category with the given ID exists
        $check = Category::find($row['id']);
        if (!$check) {
            // If the category doesn't exist, insert data into the database
            $id = Category::insertGetId($row);   
            $i++;
            $blogTransArr = array(
                'category_id'=> $row['id'],
                'language_code'=>'en',
                'name' => $row['name'], 
                'created_at'=> now()
            );
            CategoryTranslation::insertGetId($blogTransArr);
            $blogCatArr = array(
                'category_id'=> $row['id'],
                'blog_id' => $i, 
                'type' => 'category', 
                'created_at'=> now()
            );
            BlogCategory::insertGetId($blogCatArr);
        }
    }
    }

}
