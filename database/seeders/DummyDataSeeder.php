<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\CPL;
use App\Models\ProfilLulusan;
use App\Models\Semester;
use App\Models\Referensi;
use App\Models\RPS;
use App\Enums\CPKategori;
use App\Enums\Jenjang;
use App\Enums\SemesterTipe;
use App\Enums\RPSStatus;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        $fakTeknik = Fakultas::create(['tenant_id' => $tenant->id, 'name' => 'Fakultas Teknik', 'code' => 'FT', 'dekan' => 'Dr. Teknik']);
        $fakEkonomi = Fakultas::create(['tenant_id' => $tenant->id, 'name' => 'Fakultas Ekonomi', 'code' => 'FE', 'dekan' => 'Dr. Ekonomi']);

        $prodiTI = ProgramStudi::create(['fakultas_id' => $fakTeknik->id, 'name' => 'Teknik Informatika', 'code' => 'TI', 'jenjang' => Jenjang::S1->value]);
        $prodiSI = ProgramStudi::create(['fakultas_id' => $fakTeknik->id, 'name' => 'Sistem Informasi', 'code' => 'SI', 'jenjang' => Jenjang::S1->value]);
        $prodiMJ = ProgramStudi::create(['fakultas_id' => $fakEkonomi->id, 'name' => 'Manajemen', 'code' => 'MJ', 'jenjang' => Jenjang::S1->value]);

        $kuriTI = Kurikulum::create(['program_studi_id' => $prodiTI->id, 'name' => 'Kurikulum TI 2024', 'tahun_mulai' => 2024, 'tahun_selesai' => 2028, 'total_sks' => 144, 'is_active' => true]);
        $kuriMJ = Kurikulum::create(['program_studi_id' => $prodiMJ->id, 'name' => 'Kurikulum MJ 2024', 'tahun_mulai' => 2024, 'tahun_selesai' => 2028, 'total_sks' => 144, 'is_active' => true]);

        Semester::create(['tenant_id' => $tenant->id, 'name' => 'Ganjil 2025/2026', 'tipe' => SemesterTipe::Ganjil->value, 'tahun_akademik' => '2025/2026', 'tanggal_mulai' => '2025-09-01', 'tanggal_selesai' => '2026-01-31', 'is_active' => true]);
        Semester::create(['tenant_id' => $tenant->id, 'name' => 'Genap 2025/2026', 'tipe' => SemesterTipe::Genap->value, 'tahun_akademik' => '2025/2026', 'tanggal_mulai' => '2026-02-01', 'tanggal_selesai' => '2026-07-31', 'is_active' => false]);

        $pl1 = ProfilLulusan::create(['program_studi_id' => $prodiTI->id, 'name' => 'Software Engineer', 'deskripsi' => 'Mampu merancang dan mengembangkan perangkat lunak']);
        $pl2 = ProfilLulusan::create(['program_studi_id' => $prodiTI->id, 'name' => 'Data Scientist', 'deskripsi' => 'Mampu menganalisis data dan membangun model AI']);

        $cpls = [
            ['program_studi_id' => $prodiTI->id, 'code' => 'CPL-S-01', 'deskripsi' => 'Bertakwa kepada Tuhan YME dan mampu menunjukkan sikap religius', 'kategori' => CPKategori::Sikap->value],
            ['program_studi_id' => $prodiTI->id, 'code' => 'CPL-S-02', 'deskripsi' => 'Menjunjung tinggi nilai kemanusiaan dalam menjalankan tugas', 'kategori' => CPKategori::Sikap->value],
            ['program_studi_id' => $prodiTI->id, 'code' => 'CPL-P-01', 'deskripsi' => 'Menguasai konsep teoritis ilmu komputer dan rekayasa perangkat lunak', 'kategori' => CPKategori::Pengetahuan->value],
            ['program_studi_id' => $prodiTI->id, 'code' => 'CPL-P-02', 'deskripsi' => 'Menguasai konsep algoritma, struktur data, dan basis data', 'kategori' => CPKategori::Pengetahuan->value],
            ['program_studi_id' => $prodiTI->id, 'code' => 'CPL-KU-01', 'deskripsi' => 'Mampu menerapkan pemikiran logis, kritis, dan sistematis', 'kategori' => CPKategori::KeterampilanUmum->value],
            ['program_studi_id' => $prodiTI->id, 'code' => 'CPL-KU-02', 'deskripsi' => 'Mampu mengelola pembelajaran secara mandiri', 'kategori' => CPKategori::KeterampilanUmum->value],
            ['program_studi_id' => $prodiTI->id, 'code' => 'CPL-KK-01', 'deskripsi' => 'Mampu mengembangkan aplikasi berbasis web dan mobile', 'kategori' => CPKategori::KeterampilanKhusus->value],
            ['program_studi_id' => $prodiTI->id, 'code' => 'CPL-KK-02', 'deskripsi' => 'Mampu mengelola proyek pengembangan perangkat lunak', 'kategori' => CPKategori::KeterampilanKhusus->value],
        ];
        foreach ($cpls as $c) { CPL::create($c); }

        $pl1->cpls()->sync([3, 4, 7]);
        $pl2->cpls()->sync([4, 7, 8]);

        $d1 = Dosen::create(['tenant_id' => $tenant->id, 'nidn' => '0012345601', 'name' => 'Dosen Rina', 'jabatan_fungsional' => 'Asisten Ahli', 'bidang_keahlian' => 'Software Engineering']);
        $d2 = Dosen::create(['tenant_id' => $tenant->id, 'nidn' => '0012345602', 'name' => 'Dosen Budi', 'jabatan_fungsional' => 'Lektor', 'bidang_keahlian' => 'Basis Data']);
        $d3 = Dosen::create(['tenant_id' => $tenant->id, 'nidn' => '0012345603', 'name' => 'Dr. Kaprodi', 'jabatan_fungsional' => 'Lektor Kepala', 'bidang_keahlian' => 'AI & Machine Learning']);

        $mk1 = MataKuliah::create(['kurikulum_id' => $kuriTI->id, 'name' => 'Pemrograman Web', 'code' => 'TI-101', 'sks' => 3, 'semester' => 3, 'jenis' => 'wajib', 'deskripsi' => 'Mata kuliah pengembangan aplikasi web dengan Laravel']);
        $mk2 = MataKuliah::create(['kurikulum_id' => $kuriTI->id, 'name' => 'Basis Data', 'code' => 'TI-201', 'sks' => 3, 'semester' => 4, 'jenis' => 'wajib', 'deskripsi' => 'Konsep dan implementasi sistem basis data relasional']);
        $mk3 = MataKuliah::create(['kurikulum_id' => $kuriTI->id, 'name' => 'AI & Machine Learning', 'code' => 'TI-301', 'sks' => 4, 'semester' => 6, 'jenis' => 'pilihan', 'deskripsi' => 'Pengenalan kecerdasan buatan dan machine learning']);
        $mk4 = MataKuliah::create(['kurikulum_id' => $kuriMJ->id, 'name' => 'Manajemen Keuangan', 'code' => 'MJ-101', 'sks' => 3, 'semester' => 3, 'jenis' => 'wajib']);

        $mk1->cpls()->sync([1, 3, 5, 7]);
        $mk2->cpls()->sync([3, 4, 5, 7]);
        $mk3->cpls()->sync([3, 5, 7, 8]);

        $mk1->dosens()->sync([$d1->id, $d2->id]);
        $mk2->dosens()->sync([$d2->id]);
        $mk3->dosens()->sync([$d3->id]);

        Referensi::create(['tenant_id' => $tenant->id, 'judul' => 'Clean Code: A Handbook of Agile Software Craftsmanship', 'penulis' => 'Robert C. Martin', 'tahun' => 2008, 'penerbit' => 'Prentice Hall', 'format' => 'APA']);
        Referensi::create(['tenant_id' => $tenant->id, 'judul' => 'Design Patterns: Elements of Reusable Object-Oriented Software', 'penulis' => 'Gamma, Helm, Johnson, Vlissides', 'tahun' => 1994, 'penerbit' => 'Addison-Wesley', 'format' => 'APA']);
        Referensi::create(['tenant_id' => $tenant->id, 'judul' => 'Database System Concepts', 'penulis' => 'Silberschatz, Korth, Sudarshan', 'tahun' => 2020, 'penerbit' => 'McGraw-Hill', 'format' => 'APA']);
        Referensi::create(['tenant_id' => $tenant->id, 'judul' => 'Artificial Intelligence: A Modern Approach', 'penulis' => 'Russell & Norvig', 'tahun' => 2021, 'penerbit' => 'Pearson', 'format' => 'APA']);
        Referensi::create(['tenant_id' => $tenant->id, 'judul' => 'Laravel: Up & Running', 'penulis' => 'Matt Stauffer', 'tahun' => 2023, 'penerbit' => "O\'Reilly Media", 'format' => 'APA']);

        $semester = Semester::first();
        $dosenUser = User::where('email', 'dosen@rpsobe.id')->first();
        $dosenUser2 = User::where('email', 'dosen2@rpsobe.id')->first();

        if ($dosenUser) {
            $rps1 = RPS::create(['mata_kuliah_id' => $mk1->id, 'semester_id' => $semester->id, 'user_id' => $dosenUser->id, 'status' => RPSStatus::Draft->value, 'version_label' => 'v0.1']);
            $rps1->cpl()->sync([3, 5, 7]);

            $rps2 = RPS::create(['mata_kuliah_id' => $mk2->id, 'semester_id' => $semester->id, 'user_id' => $dosenUser2->id, 'status' => RPSStatus::Review->value, 'version_label' => 'v1.0']);
            $rps2->cpl()->sync([3, 4, 5]);

            $rps3 = RPS::create(['mata_kuliah_id' => $mk3->id, 'semester_id' => $semester->id, 'user_id' => $dosenUser->id, 'status' => RPSStatus::Published->value, 'version_label' => 'v1.0']);
            $rps3->cpl()->sync([3, 5, 7, 8]);
        }
    }
}
