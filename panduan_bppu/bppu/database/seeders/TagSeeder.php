<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'BPPU', 'slug' => 'bppu'],
            ['name' => 'IKIP Siliwangi', 'slug' => 'ikip-siliwangi'],
            ['name' => 'Pendidikan', 'slug' => 'pendidikan'],
            ['name' => 'Kewirausahaan', 'slug' => 'kewirausahaan'],
            ['name' => 'Pengembangan Usaha', 'slug' => 'pengembangan-usaha'],
            ['name' => 'Unit Usaha', 'slug' => 'unit-usaha'],
            ['name' => 'Laporan Keuangan', 'slug' => 'laporan-keuangan'],
            ['name' => 'Mahasiswa', 'slug' => 'mahasiswa'],
            ['name' => 'Dosen', 'slug' => 'dosen'],
            ['name' => 'Kampus', 'slug' => 'kampus'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
