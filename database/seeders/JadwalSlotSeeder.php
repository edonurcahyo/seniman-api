<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalSlotSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 30; $i++) {

            $tanggal = Carbon::today()->addDays($i)->format('Y-m-d');

            $jadwalId = DB::table('jadwal')->insertGetId([
                'tanggal' => $tanggal,
                'status' => 'tersedia',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $jamMulai = Carbon::createFromTime(10, 0, 0);
            $jamTutup = Carbon::createFromTime(22, 0, 0);

            while ($jamMulai < $jamTutup) {

                $jamSelesai = (clone $jamMulai)->addMinutes(45);

                DB::table('slot_waktu')->insert([
                    'jadwal_id' => $jadwalId,
                    'jam_mulai' => $jamMulai->format('H:i:s'),
                    'jam_selesai' => $jamSelesai->format('H:i:s'),
                    'status' => 'tersedia',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $jamMulai->addMinutes(45);
            }
        }
    }
}