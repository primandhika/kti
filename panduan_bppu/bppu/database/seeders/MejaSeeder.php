<?php

namespace Database\Seeders;

use App\Models\LokasiMeja;
use App\Models\Meja;
use Illuminate\Database\Seeder;

class MejaSeeder extends Seeder
{
    public function run(): void
    {
        $lokasis = [
            ['nama' => 'Kantin G',           'kode' => 'KG',  'display_order' => 1],
            ['nama' => 'Kantin B',           'kode' => 'KB',  'display_order' => 2],
            ['nama' => 'Gazebo Kantin B',    'kode' => 'GKB', 'display_order' => 3],
            ['nama' => 'Dalam Kantin G',     'kode' => 'DKG', 'display_order' => 4],
            ['nama' => 'Gedung A',           'kode' => 'GA',  'display_order' => 5],
            ['nama' => 'Gedung B',           'kode' => 'GB',  'display_order' => 6],
            ['nama' => 'Gedung C',           'kode' => 'GC',  'display_order' => 7],
            ['nama' => 'Gedung D',           'kode' => 'GD',  'display_order' => 8],
            ['nama' => 'Gedung E',           'kode' => 'GE',  'display_order' => 9],
            ['nama' => 'Gedung F',           'kode' => 'GF',  'display_order' => 10],
            ['nama' => 'Masjid',             'kode' => 'MSJ', 'display_order' => 11],
            ['nama' => 'Laboratorium Komputer', 'kode' => 'LAB', 'display_order' => 12],
            ['nama' => 'Aula',               'kode' => 'ALA', 'display_order' => 13],
            ['nama' => 'Gedung I (Rektorat)', 'kode' => 'GI', 'display_order' => 14],
        ];

        foreach ($lokasis as $data) {
            LokasiMeja::firstOrCreate(['kode' => $data['kode']], $data);
        }

        // Seed meja: 01-02 di Dalam Kantin G
        $dkg = LokasiMeja::where('kode', 'DKG')->first();
        if ($dkg) {
            foreach (range(1, 2) as $n) {
                $kode = str_pad($n, 2, '0', STR_PAD_LEFT);
                Meja::firstOrCreate(
                    ['lokasi_meja_id' => $dkg->id, 'kode_meja' => $kode],
                    ['nama' => 'Meja ' . $kode, 'nomor' => $kode, 'display_order' => $n]
                );
            }
        }

        // Seed meja: 03-09 di Kantin G
        $kg = LokasiMeja::where('kode', 'KG')->first();
        if ($kg) {
            foreach (range(3, 9) as $n) {
                $kode = str_pad($n, 2, '0', STR_PAD_LEFT);
                Meja::firstOrCreate(
                    ['lokasi_meja_id' => $kg->id, 'kode_meja' => $kode],
                    ['nama' => 'Meja ' . $kode, 'nomor' => $kode, 'display_order' => $n - 2]
                );
            }
        }

        // Seed meja: 01-04 di Gazebo Kantin B
        $gkb = LokasiMeja::where('kode', 'GKB')->first();
        if ($gkb) {
            foreach (range(1, 4) as $n) {
                $kode = str_pad($n, 2, '0', STR_PAD_LEFT);
                Meja::firstOrCreate(
                    ['lokasi_meja_id' => $gkb->id, 'kode_meja' => $kode],
                    ['nama' => 'Meja ' . $kode, 'nomor' => $kode, 'display_order' => $n]
                );
            }
        }

        // Seed meja di Gedung I (Rektorat)
        $gi = LokasiMeja::where('kode', 'GI')->first();
        if ($gi) {
            $mejasGI = [
                ['kode_meja' => 'GI-RKT',  'nama' => 'Ruang Rektor',          'nomor' => '1', 'display_order' => 1],
                ['kode_meja' => 'GI-WRA',  'nama' => 'Meja WR Akademik',      'nomor' => '2', 'display_order' => 2],
                ['kode_meja' => 'GI-WRK',  'nama' => 'Meja WR Keuangan',      'nomor' => '3', 'display_order' => 3],
                ['kode_meja' => 'GI-WRS',  'nama' => 'Meja WR SDM',           'nomor' => '4', 'display_order' => 4],
                ['kode_meja' => 'GI-FO',   'nama' => 'Meja Front Office (FO)','nomor' => '5', 'display_order' => 5],
                ['kode_meja' => 'GI-PPG1', 'nama' => 'Meja PPG 1',            'nomor' => '6', 'display_order' => 6],
                ['kode_meja' => 'GI-PPG2', 'nama' => 'Meja PPG 2',            'nomor' => '7', 'display_order' => 7],
                ['kode_meja' => 'GI-SEKR', 'nama' => 'Sekretariat Rektorat',  'nomor' => '8', 'display_order' => 8],
            ];
            foreach ($mejasGI as $data) {
                Meja::firstOrCreate(
                    ['lokasi_meja_id' => $gi->id, 'kode_meja' => $data['kode_meja']],
                    array_merge($data, ['lokasi_meja_id' => $gi->id])
                );
            }
        }

        // Seed meja di Gedung A
        $ga = LokasiMeja::where('kode', 'GA')->first();
        if ($ga) {
            $mejasGA = [
                ['kode_meja' => 'GA-SDM',  'nama' => 'Meja SDM',          'nomor' => '1', 'display_order' => 1],
                ['kode_meja' => 'GA-KMH',  'nama' => 'Meja Kemahasiswaan','nomor' => '2', 'display_order' => 2],
            ];
            foreach ($mejasGA as $data) {
                Meja::firstOrCreate(
                    ['lokasi_meja_id' => $ga->id, 'kode_meja' => $data['kode_meja']],
                    array_merge($data, ['lokasi_meja_id' => $ga->id])
                );
            }
        }
    }
}
