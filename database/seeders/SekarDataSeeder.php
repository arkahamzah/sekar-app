<?php
// database/seeders/SekarDataSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SekarDataSeeder extends Seeder
{
    public function run()
    {
        // Insert M_JAJARAN data
        DB::table('m_jajaran')->insert([
            ['ID' => 1, 'NAMA_JAJARAN' => 'KETUA UMUM', 'IS_AKTIF' => '1'],
            ['ID' => 2, 'NAMA_JAJARAN' => 'WAKIL SEKRETARIS JENDRAL', 'IS_AKTIF' => '1'],
            ['ID' => 3, 'NAMA_JAJARAN' => 'SEKRETARIS JENDRAL', 'IS_AKTIF' => '1'],
        ]);

        // Insert P_PARAMS data
        DB::table('p_params')->insert([
            [
                'ID' => 1,
                'NOMINAL_IURAN_WAJIB' => '25000',
                'NOMINAL_BANPERS' => '20000',
                'CREATED_BY' => '401031',
                'CREATED_AT' => '2025-07-22 09:20:34',
                'TAHUN' => '2025',
                'IS_AKTIF' => '1'
            ]
        ]);

        // Insert T_KARYAWAN data
        DB::table('t_karyawan')->insert([
            [
                'ID' => 1,
                'N_NIK' => '401031',
                'V_NAMA_KARYAWAN' => 'ADHIE PRANATHA',
                'V_SHORT_UNIT' => 'HCM RISK OPERATION',
                'V_SHORT_POSISI' => 'OFF 2 HCM & RISK OPERATION',
                'C_KODE_POSISI' => 'COP0200001',
                'C_KODE_UNIT' => 'DIT-1020202',
                'V_SHORT_DIVISI' => 'DIVISI INFORMATION SYSTEM',
                'V_BAND_POSISI' => 'V',
                'C_KODE_DIVISI' => 'DIV-DIT',
                'C_PERSONNEL_AREA' => 'HCP2',
                'C_PERSONNEL_SUB_AREA' => 'BN00',
                'V_KOTA_GEDUNG' => 'BANDUNG'
            ]
        ]);

        // Insert T_SEKAR_ROLES data
        DB::table('t_sekar_roles')->insert([
            ['ID' => 1, 'NAME' => 'ADM', 'DESC' => 'Administrator', 'IS_AKTIF' => '1'],
            ['ID' => 2, 'NAME' => 'ADMIN_DPP', 'DESC' => 'Admin DPP', 'IS_AKTIF' => '1'],
            ['ID' => 3, 'NAME' => 'ADMIN_DPW', 'DESC' => 'Admin DPW', 'IS_AKTIF' => '1'],
            ['ID' => 4, 'NAME' => 'ADMIN_DPD', 'DESC' => 'Admin DPD', 'IS_AKTIF' => '1'],
        ]);

        // Insert T_SEKAR_PENGURUS data
        DB::table('t_sekar_pengurus')->insert([
            [
                'ID' => 2,
                'N_NIK' => '401031',
                'V_SHORT_POSISI' => 'OFF 2 HCM MANAGEMENT',
                'V_SHORT_UNIT' => 'HCM & RISK OPERATION',
                'CREATED_BY' => '401031',
                'CREATED_AT' => '2025-07-22 09:10:02',
                'UPDATED_BY' => null,
                'UPDATED_AT' => null,
                'DPP' => null,
                'DPW' => null,
                'DPD' => '',
                'ID_ROLES' => 1
            ],
            [
                'ID' => 3,
                'N_NIK' => '980269',
                'V_SHORT_POSISI' => 'MGR RISK OPERATION',
                'V_SHORT_UNIT' => 'RISK OPERATION',
                'CREATED_BY' => '980269',
                'CREATED_AT' => '2025-07-22 09:10:58',
                'UPDATED_BY' => null,
                'UPDATED_AT' => null,
                'DPP' => 'BN00',
                'DPW' => null,
                'DPD' => null,
                'ID_ROLES' => 2
            ]
        ]);

        // Insert T_SEKAR_JAJARAN data
        DB::table('t_sekar_jajaran')->insert([
            [
                'ID' => 1,
                'N_NIK' => '990009',
                'V_NAMA_KARYAWAN' => 'JHON CRUYF',
                'ID_JAJARAN' => '1',
                'START_DATE' => '2025-07-22 09:11:57',
                'END_DATE' => '2026-02-22 09:12:01',
                'CREATED_BY' => '401031',
                'CREATED_AT' => '2025-07-22 09:12:12',
                'IS_AKTIF' => '1'
            ],
            [
                'ID' => 2,
                'N_NIK' => '980303',
                'V_NAMA_KARYAWAN' => 'EMIL DARDAK',
                'ID_JAJARAN' => '2',
                'START_DATE' => '2025-07-22 09:12:49',
                'END_DATE' => '2026-02-22 09:12:01',
                'CREATED_BY' => '401031',
                'CREATED_AT' => '2025-07-22 09:12:12',
                'IS_AKTIF' => '1'
            ]
        ]);

        // Insert T_EX_ANGGOTA data
        DB::table('t_ex_anggota')->insert([
            [
                'ID' => 1,
                'N_NIK' => '401032',
                'V_NAMA_KARYAWAN' => 'DEBORAH',
                'V_SHORT_POSISI' => 'OFF 3 SYSTEM INFORMATION',
                'V_SHORT_DIVISI' => 'DIVISI INFORMATION TECHNOLOGY',
                'TGL_KELUAR' => '2025-07-22 09:16:30',
                'DPP' => null,
                'DPW' => null,
                'DPD' => 'BD00',
                'V_KOTA_GEDUNG' => 'BANDUNG',
                'NO_TELP' => '0809800029',
                'CREATED_BY' => '401031',
                'CREATED_AT' => '2025-07-22 09:16:54'
            ]
        ]);

        // Insert T_IURAN data
        DB::table('t_iuran')->insert([
            [
                'ID' => 1,
                'N_NIK' => '401031',
                'IURAN_WAJIB' => '25000',
                'IURAN_SUKARELA' => '5000',
                'CREATED_BY' => '401031',
                'CREATED_AT' => '2025-07-22 09:15:40',
                'UPDATE_BY' => null,
                'UPDATED_AT' => null
            ]
        ]);

        // Add more sample karyawan data for realistic dashboard
        $additionalKaryawan = [
            [
                'N_NIK' => '401033',
                'V_NAMA_KARYAWAN' => 'BUDI SANTOSO',
                'V_SHORT_UNIT' => 'FINANCE OPERATION',
                'V_SHORT_POSISI' => 'STAFF FINANCE',
                'C_KODE_POSISI' => 'FIN0100001',
                'C_KODE_UNIT' => 'FIN-1010101',
                'V_SHORT_DIVISI' => 'DIVISI FINANCE',
                'V_BAND_POSISI' => 'IV',
                'C_KODE_DIVISI' => 'DIV-FIN',
                'C_PERSONNEL_AREA' => 'HCP1',
                'C_PERSONNEL_SUB_AREA' => 'JK00',
                'V_KOTA_GEDUNG' => 'JAKARTA'
            ],
            [
                'N_NIK' => '401034',
                'V_NAMA_KARYAWAN' => 'SARI DEWI',
                'V_SHORT_UNIT' => 'HUMAN RESOURCE',
                'V_SHORT_POSISI' => 'HR SPECIALIST',
                'C_KODE_POSISI' => 'HR0200001',
                'C_KODE_UNIT' => 'HR-2020202',
                'V_SHORT_DIVISI' => 'DIVISI HUMAN RESOURCE',
                'V_BAND_POSISI' => 'V',
                'C_KODE_DIVISI' => 'DIV-HR',
                'C_PERSONNEL_AREA' => 'HCP2',
                'C_PERSONNEL_SUB_AREA' => 'SB00',
                'V_KOTA_GEDUNG' => 'SURABAYA'
            ]
        ];

        foreach ($additionalKaryawan as $karyawan) {
            DB::table('t_karyawan')->insert($karyawan);
        }
    }
}
