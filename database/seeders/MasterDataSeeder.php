<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Kurikulum;
use App\Models\Semester;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\ProfilLulusan;
use App\Models\CPL;
use App\Models\Referensi;
use App\Enums\Jenjang;
use App\Enums\CPKategori;
use App\Enums\SemesterTipe;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 1;

        $fakultasTeknik = Fakultas::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'FT'],
            [
                'tenant_id' => $tenantId,
                'name' => 'Fakultas Teknik',
                'code' => 'FT',
                'dekan' => 'Prof. Dr. Ir. Budi Santoso, M.Eng.',
                'akreditasi' => 'Unggul',
            ]
        );

        $fakultasEkonomi = Fakultas::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'FE'],
            [
                'tenant_id' => $tenantId,
                'name' => 'Fakultas Ekonomi',
                'code' => 'FE',
                'dekan' => 'Prof. Dr. Sri Wahyuni, S.E., M.M.',
                'akreditasi' => 'Unggul',
            ]
        );

        $prodiTI = ProgramStudi::firstOrCreate(
            ['fakultas_id' => $fakultasTeknik->id, 'code' => 'TI-S1'],
            [
                'fakultas_id' => $fakultasTeknik->id,
                'name' => 'Teknik Informatika',
                'code' => 'TI-S1',
                'jenjang' => Jenjang::S1,
                'akreditasi' => 'Unggul',
                'kaprodi_name' => 'Dr. Ahmad Fauzi, S.Kom., M.T.',
            ]
        );

        $prodiManajemen = ProgramStudi::firstOrCreate(
            ['fakultas_id' => $fakultasEkonomi->id, 'code' => 'MNJ-S1'],
            [
                'fakultas_id' => $fakultasEkonomi->id,
                'name' => 'Manajemen',
                'code' => 'MNJ-S1',
                'jenjang' => Jenjang::S1,
                'akreditasi' => 'A',
                'kaprodi_name' => 'Dr. Rina Wijaya, S.E., M.Si.',
            ]
        );

        $kurikulumTI = Kurikulum::firstOrCreate(
            ['program_studi_id' => $prodiTI->id, 'name' => 'Kurikulum 2024-2028'],
            [
                'program_studi_id' => $prodiTI->id,
                'name' => 'Kurikulum 2024-2028',
                'tahun_mulai' => 2024,
                'tahun_selesai' => 2028,
                'total_sks' => 144,
                'is_active' => true,
            ]
        );

        $kurikulumManajemen = Kurikulum::firstOrCreate(
            ['program_studi_id' => $prodiManajemen->id, 'name' => 'Kurikulum 2024-2028'],
            [
                'program_studi_id' => $prodiManajemen->id,
                'name' => 'Kurikulum 2024-2028',
                'tahun_mulai' => 2024,
                'tahun_selesai' => 2028,
                'total_sks' => 144,
                'is_active' => true,
            ]
        );

        Semester::firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'Semester Ganjil 2025/2026'],
            [
                'tenant_id' => $tenantId,
                'name' => 'Semester Ganjil 2025/2026',
                'tipe' => SemesterTipe::Ganjil,
                'tahun_akademik' => '2025/2026',
                'tanggal_mulai' => '2025-09-01',
                'tanggal_selesai' => '2026-01-31',
                'is_active' => true,
            ]
        );

        Semester::firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'Semester Genap 2025/2026'],
            [
                'tenant_id' => $tenantId,
                'name' => 'Semester Genap 2025/2026',
                'tipe' => SemesterTipe::Genap,
                'tahun_akademik' => '2025/2026',
                'tanggal_mulai' => '2026-02-01',
                'tanggal_selesai' => '2026-07-31',
                'is_active' => true,
            ]
        );

        $mkTI = [
            [
                'name' => 'Pemrograman Dasar',
                'code' => 'TIF101',
                'sks' => 4,
                'semester' => 1,
                'jenis' => 'wajib',
                'deskripsi' => 'Mata kuliah ini membahas konsep dasar pemrograman menggunakan bahasa pemrograman Python, meliputi tipe data, struktur kontrol, fungsi, dan pengenalan algoritma.',
            ],
            [
                'name' => 'Struktur Data dan Algoritma',
                'code' => 'TIF102',
                'sks' => 3,
                'semester' => 2,
                'jenis' => 'wajib',
                'deskripsi' => 'Mata kuliah ini membahas berbagai struktur data seperti array, linked list, stack, queue, tree, graph serta algoritma pencarian dan pengurutan.',
            ],
            [
                'name' => 'Pemrograman Web Lanjut',
                'code' => 'TIF301',
                'sks' => 3,
                'semester' => 5,
                'jenis' => 'pilihan',
                'deskripsi' => 'Mata kuliah ini membahas pengembangan aplikasi web modern menggunakan framework Laravel, REST API, dan konsep full-stack development.',
            ],
        ];

        $mkTIIds = [];
        foreach ($mkTI as $mk) {
            $mataKuliah = MataKuliah::firstOrCreate(
                ['kurikulum_id' => $kurikulumTI->id, 'code' => $mk['code']],
                array_merge(['kurikulum_id' => $kurikulumTI->id], $mk)
            );
            $mkTIIds[] = $mataKuliah->id;
        }

        $mkManajemen = [
            [
                'name' => 'Pengantar Manajemen',
                'code' => 'MNJ101',
                'sks' => 3,
                'semester' => 1,
                'jenis' => 'wajib',
                'deskripsi' => 'Mata kuliah ini membahas konsep dasar manajemen meliputi perencanaan, pengorganisasian, pengarahan, dan pengendalian dalam organisasi.',
            ],
            [
                'name' => 'Manajemen Keuangan',
                'code' => 'MNJ201',
                'sks' => 3,
                'semester' => 3,
                'jenis' => 'wajib',
                'deskripsi' => 'Mata kuliah ini membahas prinsip-prinsip pengelolaan keuangan perusahaan, analisis laporan keuangan, dan pengambilan keputusan investasi.',
            ],
            [
                'name' => 'Kewirausahaan Digital',
                'code' => 'MNJ302',
                'sks' => 2,
                'semester' => 6,
                'jenis' => 'pilihan',
                'deskripsi' => 'Mata kuliah ini membahas konsep kewirausahaan di era digital, model bisnis startup, dan strategi pemasaran digital.',
            ],
        ];

        $mkManajemenIds = [];
        foreach ($mkManajemen as $mk) {
            $mataKuliah = MataKuliah::firstOrCreate(
                ['kurikulum_id' => $kurikulumManajemen->id, 'code' => $mk['code']],
                array_merge(['kurikulum_id' => $kurikulumManajemen->id], $mk)
            );
            $mkManajemenIds[] = $mataKuliah->id;
        }

        $dosens = [
            [
                'nidn' => '0012057801',
                'name' => 'Dr. Ahmad Fauzi, S.Kom., M.T.',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'S.Kom., M.T.',
                'jabatan_fungsional' => 'Lektor Kepala',
                'bidang_keahlian' => 'Artificial Intelligence',
                'email' => 'ahmad.fauzi@univ.ac.id',
                'phone' => '081234567890',
            ],
            [
                'nidn' => '0015088202',
                'name' => 'Dian Permata, S.Kom., M.Cs.',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Kom., M.Cs.',
                'jabatan_fungsional' => 'Lektor',
                'bidang_keahlian' => 'Software Engineering',
                'email' => 'dian.permata@univ.ac.id',
                'phone' => '081234567891',
            ],
            [
                'nidn' => '0020108003',
                'name' => 'Prof. Dr. Sri Wahyuni, S.E., M.M.',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'S.E., M.M.',
                'jabatan_fungsional' => 'Guru Besar',
                'bidang_keahlian' => 'Manajemen Strategik',
                'email' => 'sri.wahyuni@univ.ac.id',
                'phone' => '081234567892',
            ],
        ];

        $dosenIds = [];
        foreach ($dosens as $data) {
            $dosen = Dosen::firstOrCreate(
                ['nidn' => $data['nidn']],
                array_merge(['tenant_id' => $tenantId, 'is_active' => true], $data)
            );
            $dosenIds[] = $dosen->id;
        }

        $mataKuliahTI = MataKuliah::whereIn('id', $mkTIIds)->get();
        foreach ($mataKuliahTI as $index => $mk) {
            if ($index < 2) {
                $mk->dosens()->syncWithoutDetaching([$dosenIds[0]]);
                if ($index < 1) {
                    $mk->dosens()->syncWithoutDetaching([$dosenIds[1]]);
                }
            }
        }

        $mataKuliahManajemen = MataKuliah::whereIn('id', $mkManajemenIds)->get();
        foreach ($mataKuliahManajemen as $index => $mk) {
            $mk->dosens()->syncWithoutDetaching([$dosenIds[2]]);
        }

        $profilTI = [
            [
                'name' => 'Software Engineer',
                'deskripsi' => 'Lulusan mampu merancang, mengembangkan, dan menguji perangkat lunak berkualitas sesuai kebutuhan industri.',
            ],
            [
                'name' => 'Data Analyst',
                'deskripsi' => 'Lulusan mampu mengumpulkan, mengolah, dan menganalisis data untuk mendukung pengambilan keputusan berbasis data.',
            ],
        ];

        $profilTIIds = [];
        foreach ($profilTI as $data) {
            $profil = ProfilLulusan::firstOrCreate(
                ['program_studi_id' => $prodiTI->id, 'name' => $data['name']],
                array_merge(['program_studi_id' => $prodiTI->id], $data)
            );
            $profilTIIds[] = $profil->id;
        }

        $profilManajemen = [
            [
                'name' => 'Manajer Operasional',
                'deskripsi' => 'Lulusan mampu mengelola operasional organisasi secara efektif dan efisien.',
            ],
            [
                'name' => 'Business Analyst',
                'deskripsi' => 'Lulusan mampu menganalisis proses bisnis dan memberikan rekomendasi strategis untuk peningkatan kinerja organisasi.',
            ],
        ];

        $profilManajemenIds = [];
        foreach ($profilManajemen as $data) {
            $profil = ProfilLulusan::firstOrCreate(
                ['program_studi_id' => $prodiManajemen->id, 'name' => $data['name']],
                array_merge(['program_studi_id' => $prodiManajemen->id], $data)
            );
            $profilManajemenIds[] = $profil->id;
        }

        $cplTI = [
            ['code' => 'CPL-S1', 'deskripsi' => 'Bertakwa kepada Tuhan Yang Maha Esa dan mampu menunjukkan sikap religius.', 'kategori' => CPKategori::Sikap],
            ['code' => 'CPL-S2', 'deskripsi' => 'Menjunjung tinggi nilai kemanusiaan dalam menjalankan tugas berdasarkan agama, moral, dan etika.', 'kategori' => CPKategori::Sikap],
            ['code' => 'CPL-P1', 'deskripsi' => 'Menguasai konsep teoritis bidang pengetahuan Ilmu Komputer secara umum.', 'kategori' => CPKategori::Pengetahuan],
            ['code' => 'CPL-KU1', 'deskripsi' => 'Mampu menerapkan pemikiran logis, kritis, sistematis, dan inovatif dalam konteks pengembangan IPTEKS.', 'kategori' => CPKategori::KeterampilanUmum],
            ['code' => 'CPL-KU2', 'deskripsi' => 'Mampu menunjukkan kinerja mandiri, bermutu, dan terukur.', 'kategori' => CPKategori::KeterampilanUmum],
            ['code' => 'CPL-KK1', 'deskripsi' => 'Mampu mengembangkan perangkat lunak dengan metodologi yang tepat dan terdokumentasi.', 'kategori' => CPKategori::KeterampilanKhusus],
        ];

        $cplTIIds = [];
        foreach ($cplTI as $data) {
            $cpl = CPL::firstOrCreate(
                ['program_studi_id' => $prodiTI->id, 'code' => $data['code']],
                array_merge(['program_studi_id' => $prodiTI->id], $data)
            );
            $cplTIIds[] = $cpl->id;
        }

        $cplManajemen = [
            ['code' => 'CPL-S1', 'deskripsi' => 'Bertakwa kepada Tuhan Yang Maha Esa dan mampu menunjukkan sikap religius.', 'kategori' => CPKategori::Sikap],
            ['code' => 'CPL-S2', 'deskripsi' => 'Menjunjung tinggi nilai kemanusiaan dalam menjalankan tugas berdasarkan agama, moral, dan etika.', 'kategori' => CPKategori::Sikap],
            ['code' => 'CPL-P1', 'deskripsi' => 'Menguasai konsep teoritis bidang Manajemen secara umum dan mendalam.', 'kategori' => CPKategori::Pengetahuan],
            ['code' => 'CPL-P2', 'deskripsi' => 'Menguasai konsep dan teknik pengambilan keputusan manajerial yang tepat.', 'kategori' => CPKategori::Pengetahuan],
            ['code' => 'CPL-KU1', 'deskripsi' => 'Mampu menerapkan pemikiran logis, kritis, sistematis, dan inovatif dalam bidang manajemen.', 'kategori' => CPKategori::KeterampilanUmum],
            ['code' => 'CPL-KK1', 'deskripsi' => 'Mampu menyusun perencanaan strategis pada level organisasi.', 'kategori' => CPKategori::KeterampilanKhusus],
        ];

        $cplManajemenIds = [];
        foreach ($cplManajemen as $data) {
            $cpl = CPL::firstOrCreate(
                ['program_studi_id' => $prodiManajemen->id, 'code' => $data['code']],
                array_merge(['program_studi_id' => $prodiManajemen->id], $data)
            );
            $cplManajemenIds[] = $cpl->id;
        }

        foreach ($profilTIIds as $profilId) {
            $profil = ProfilLulusan::find($profilId);
            $profil->cpls()->syncWithoutDetaching($cplTIIds);
        }

        foreach ($profilManajemenIds as $profilId) {
            $profil = ProfilLulusan::find($profilId);
            $profil->cpls()->syncWithoutDetaching($cplManajemenIds);
        }

        $mkTICPL = [
            $mkTIIds[0] => [$cplTIIds[2], $cplTIIds[3], $cplTIIds[5]], // Pemrograman Dasar -> P1, KU1, KK1
            $mkTIIds[1] => [$cplTIIds[2], $cplTIIds[3], $cplTIIds[5]], // Struktur Data -> P1, KU1, KK1
            $mkTIIds[2] => [$cplTIIds[3], $cplTIIds[4], $cplTIIds[5]], // Web Lanjut -> KU1, KU2, KK1
        ];
        foreach ($mkTICPL as $mkId => $cplIds) {
            MataKuliah::find($mkId)->cpls()->syncWithoutDetaching($cplIds);
        }

        $mkManajemenCPL = [
            $mkManajemenIds[0] => [$cplManajemenIds[2], $cplManajemenIds[4]], // Pengantar Manajemen -> P1, KU1
            $mkManajemenIds[1] => [$cplManajemenIds[2], $cplManajemenIds[3], $cplManajemenIds[5]], // Keuangan -> P1, P2, KK1
            $mkManajemenIds[2] => [$cplManajemenIds[4], $cplManajemenIds[5]], // Kewirausahaan -> KU1, KK1
        ];
        foreach ($mkManajemenCPL as $mkId => $cplIds) {
            MataKuliah::find($mkId)->cpls()->syncWithoutDetaching($cplIds);
        }

        $referensis = [
            [
                'judul' => 'Software Engineering: A Practitioner\'s Approach',
                'penulis' => 'Roger S. Pressman',
                'tahun' => '2020',
                'penerbit' => 'McGraw-Hill',
                'format' => 'APA',
                'url' => null,
            ],
            [
                'judul' => 'Design Patterns: Elements of Reusable Object-Oriented Software',
                'penulis' => 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides',
                'tahun' => '1994',
                'penerbit' => 'Addison-Wesley',
                'format' => 'APA',
                'url' => null,
            ],
            [
                'judul' => 'Management: Principles and Applications',
                'penulis' => 'Ricky W. Griffin',
                'tahun' => '2021',
                'penerbit' => 'Cengage Learning',
                'format' => 'APA',
                'url' => null,
            ],
            [
                'judul' => 'Panduan Penyusunan Kurikulum Pendidikan Tinggi di Era Industri 4.0',
                'penulis' => 'Kemendikbudristek',
                'tahun' => '2023',
                'penerbit' => 'Direktorat Jenderal Pendidikan Tinggi',
                'format' => 'APA',
                'url' => 'https://dikti.kemdikbud.go.id/panduan-kurikulum',
            ],
            [
                'judul' => 'Outcome-Based Education: A Practical Guide',
                'penulis' => 'John Biggs dan Catherine Tang',
                'tahun' => '2019',
                'penerbit' => 'Routledge',
                'format' => 'APA',
                'url' => null,
            ],
        ];

        foreach ($referensis as $data) {
            Referensi::firstOrCreate(
                ['tenant_id' => $tenantId, 'judul' => $data['judul']],
                array_merge(['tenant_id' => $tenantId], $data)
            );
        }
    }
}
