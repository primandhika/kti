<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\User;

class ProfilPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user (or create one if not exists)
        $admin = User::first();

        if (!$admin) {
            throw new \Exception('No user found in database. Please create a user first.');
        }

        // Read content from profil.txt
        $contentPath = resource_path('content/profil.txt');

        if (!file_exists($contentPath)) {
            throw new \Exception("File not found: {$contentPath}");
        }

        $content = file_get_contents($contentPath);

        // Create or update the page
        Page::updateOrCreate(
            [
                'category' => 'profil',
                'slug' => 'tentang',
            ],
            [
                'title' => 'Tentang BPPU IKIP Siliwangi',
                'content' => $content,
                'status' => 'published',
                'meta_title' => 'Tentang BPPU IKIP Siliwangi',
                'meta_description' => 'Profil dan informasi lengkap mengenai Badan Pengelola dan Pengembangan Usaha (BPPU) IKIP Siliwangi',
                'meta_keywords' => 'BPPU, IKIP Siliwangi, profil, tentang, kantin, usaha kampus',
                'user_id' => $admin->id,
            ]
        );

        $this->command->info('Page "Tentang BPPU" created successfully!');
    }
}
