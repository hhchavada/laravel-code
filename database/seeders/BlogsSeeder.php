<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\BlogTranslation;

class BlogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $blogArr = [
            [
                'id' =>  1,
                'type' => 'post',
                'title' => 'Demo Blog 1', 
                'description' => 'Demo Blog 1', 
                'seo_title' => 'Demo Blog 1', 
                'seo_description' => 'Demo Blog 1',
                'slug' => \Helpers::createSlug('Demo Blog 1', 'blog', 0, false),
                'status' => 1,
                'order' => 1,
                'created_by' => 1,
                'schedule_date' => now(),
                'created_at' => now()
            ],
            [
                'id' =>  2,
                'type' => 'post',
                'title' => 'Demo Blog 2', 
                'description' => 'Demo Blog 2', 
                'seo_title' => 'Demo Blog 2', 
                'seo_description' => 'Demo Blog 2',
                'slug' => \Helpers::createSlug('Demo Blog 2', 'blog', 0, false),
                'status' => 1,
                'order' => 1,
                'created_by' => 1,
                'schedule_date' => now(),
                'created_at' => now()
            ],
            [
                'id' =>  3,
                'type' => 'post',
                'title' => 'Demo Blog 3', 
                'description' => 'Demo Blog 3', 
                'seo_title' => 'Demo Blog 3', 
                'seo_description' => 'Demo Blog 3',
                'slug' => \Helpers::createSlug('Demo Blog 3', 'blog', 0, false),
                'status' => 1,
                'order' => 1,
                'created_by' => 1,
                'schedule_date' => now(),
                'created_at' => now()
            ]
        ];

        foreach ($blogArr as $row) {
            $check = Blog::find($row['id']);
            if (!$check) {
                $id = Blog::insertGetId($row); 
                $blogTransArr = [
                    'blog_id' => $id,
                    'language_code' => 'en',
                    'title' => $row['title'], 
                    'description' => $row['description'], 
                    'seo_title' => $row['seo_title'], 
                    'seo_description' => $row['seo_description'],
                    'created_at' => now()
                ];
                BlogTranslation::insert($blogTransArr);
            }            
        }
    }
}
