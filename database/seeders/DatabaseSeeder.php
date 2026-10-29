<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Materi;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\UjianSoalList;
use App\Models\User;
use App\Services\UjianService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Data demo CBT TPA: pengelola, peserta, bank soal 75 butir, materi, konten, dan riwayat ujian
 * relatif terhadap hari ini. Semua akun bersandi `password`.
 */
class DatabaseSeeder extends Seeder
{
    private const PESERTA = [
        // [nama, email, HP, peluang menjawab benar]
        ['Nadia Putri Rahmawati', 'peserta@cbttpa.test', '081234500001', 0.78],
        ['Rizky Pratama', 'rizky@cbttpa.test', '081234500002', 0.62],
        ['Salsabila Azzahra', 'salsa@cbttpa.test', '081234500003', 0.85],
        ['Bagas Saputra', 'bagas@cbttpa.test', '081234500004', 0.48],
        ['Aulia Fitriani', 'aulia@cbttpa.test', '081234500005', 0.70],
        ['Dimas Ardiansyah', 'dimas@cbttpa.test', '081234500006', 0.55],
        ['Intan Permatasari', 'intan@cbttpa.test', '081234500007', 0.90],
        ['Fajar Nugroho', 'fajar@cbttpa.test', '081234500008', 0.66],
    ];

    public function run(): void
    {
        mt_srand(127);

        User::create(['name' => 'Admin CBT TPA', 'email' => 'admin@cbttpa.test', 'phone' => '081200000001', 'roles' => User::ADMIN, 'password' => Hash::make('password')]);
        User::create(['name' => 'Wulan Sari (Staf Akademik)', 'email' => 'staf@cbttpa.test', 'phone' => '081200000002', 'roles' => User::STAFF, 'password' => Hash::make('password')]);

        foreach (require __DIR__.'/data/soal_tpa.php' as $kategori => $daftar) {
            foreach ($daftar as [$pertanyaan, $a, $b, $c, $d, $kunci]) {
                Soal::create([
                    'pertanyaan' => $pertanyaan,
                    'kategori' => $kategori,
                    'jawaban_a' => $a,
                    'jawaban_b' => $b,
                    'jawaban_c' => $c,
                    'jawaban_d' => $d,
                    'kunci' => $kunci,
                ]);
            }
        }

        $this->seedKonten();
        $this->seedMateri();

        $service = app(UjianService::class);
        foreach (self::PESERTA as $i => [$nama, $email, $hp, $peluang]) {
            $user = User::create(['name' => $nama, 'email' => $email, 'phone' => $hp, 'roles' => User::PESERTA, 'password' => Hash::make('password')]);

            // Peserta utama punya dua riwayat (progres naik); peserta lain satu atau dua.
            $riwayat = $i === 0 ? [[9, $peluang - 0.15], [2, $peluang]] : ($i % 3 === 0 ? [[6, $peluang]] : [[12, $peluang - 0.1], [4, $peluang]]);
            foreach ($riwayat as [$hariLalu, $p]) {
                $this->ujianSelesai($service, $user, now()->subDays($hariLalu)->setTime(9 + $i % 5, 15), $p);
            }
        }
    }

    private function ujianSelesai(UjianService $service, User $user, $mulai, float $peluang): void
    {
        $ujian = Ujian::create(['user_id' => $user->id]);
        $ujian->forceFill(['created_at' => $mulai, 'updated_at' => $mulai])->save();

        $waktu = $mulai->copy();
        $nilai = [];
        foreach (UjianService::kategori() as $kategori) {
            $field = Ujian::field($kategori);
            foreach (Soal::where('kategori', $kategori)->inRandomOrder()->limit(config('cbt.jumlah_soal'))->get() as $soal) {
                $benar = mt_rand() / mt_getrandmax() < $peluang;
                $jawaban = $benar ? $soal->kunci : collect(['a', 'b', 'c', 'd'])->reject(fn ($x) => $x === $soal->kunci)->random();
                UjianSoalList::create(['ujian_id' => $ujian->id, 'soal_id' => $soal->id, 'jawaban' => $jawaban, 'kebenaran' => $benar]);
            }

            $ujian->forceFill([
                'timer_'.$field => config('cbt.durasi_menit'),
                'mulai_'.$field.'_at' => $waktu->copy(),
                'status_'.$field => Ujian::DONE,
            ])->save();
            $nilai[$kategori] = $service->hitung($ujian, $kategori)['nilai'];
            $ujian->forceFill(['nilai_'.$field => $nilai[$kategori]])->save();
            $waktu->addMinutes(mt_rand(18, 28));
        }

        $rata = array_sum($nilai) / count($nilai);
        $ujian->forceFill([
            'selesai_at' => $waktu,
            'hasil' => $rata >= config('cbt.nilai_lulus') ? 'Lulus' : 'Belum Lulus',
            'updated_at' => $waktu,
        ])->save();
    }

    private function seedKonten(): void
    {
        // Urutan id dipakai app: 1 = Tentang Kami, 2 = Tips & Trik.
        Content::create([
            'title' => 'Tentang CBT TPA',
            'content' => "CBT TPA adalah aplikasi latihan Tes Potensi Akademik berbasis komputer untuk calon mahasiswa, pelamar kerja, dan peserta seleksi beasiswa.\n\nSetiap ujian berisi tiga subtes: Tes Angka, Tes Verbal, dan Tes Logika. Soal diacak dari bank soal yang dikelola tim akademik, waktu dihitung oleh server, dan nilai langsung tampil begitu subtes selesai.\n\nGunakan menu Materi untuk mempelajari konsep dasar setiap subtes sebelum mencoba ujian.",
        ]);
        Content::create([
            'title' => 'Tips & Trik Mengerjakan TPA',
            'content' => "1. Kerjakan soal yang paling mudah lebih dulu. Soal yang ragu boleh dilewati lalu kembali lagi bila waktu masih ada.\n\n2. Pada deret angka, cari selisih antarsuku terlebih dahulu. Bila selisihnya tidak tetap, periksa selisih tingkat dua atau pola perkalian.\n\n3. Pada soal analogi, ubah pasangan kata menjadi kalimat hubungan, misalnya \"pena digunakan untuk menulis\", lalu uji setiap pilihan dengan kalimat yang sama.\n\n4. Pada silogisme, jangan memakai pengetahuan di luar premis. Jawaban \"tidak dapat disimpulkan\" sering benar bila premis tidak cukup.\n\n5. Perhatikan sisa waktu di pojok kanan atas. Jawaban tersimpan begitu Anda memilihnya, sehingga ujian bisa dilanjutkan bila aplikasi tertutup. Gunakan daftar nomor soal untuk memeriksa soal yang belum dijawab.",
        ]);
    }

    private function seedMateri(): void
    {
        $materi = [
            ['Deret Angka', "Deret angka menguji kemampuan menemukan pola. Langkah yang disarankan:\n\n• Hitung selisih antarsuku. Contoh 2, 5, 8, 11 memiliki selisih tetap 3.\n• Bila selisih berubah, hitung selisih dari selisih. Contoh 2, 3, 5, 8, 12 memiliki selisih 1, 2, 3, 4.\n• Periksa pola perkalian atau pembagian. Contoh 3, 6, 12, 24 dikali 2.\n• Kenali deret khusus: bilangan kuadrat (1, 4, 9, 16) dan Fibonacci (1, 1, 2, 3, 5, 8).\n\nLatih dengan menuliskan selisih di bawah setiap suku agar pola cepat terlihat."],
            ['Persentase, Pecahan & Perbandingan', "Banyak soal aritmetika TPA dapat diselesaikan tanpa kalkulator dengan pecahan sederhana.\n\n• 25% = 1/4, 12,5% = 1/8, 75% = 3/4, 20% = 1/5.\n• Diskon 20% berarti membayar 80% dari harga awal.\n• Untung (%) = (harga jual − harga beli) ÷ harga beli × 100%.\n• Perbandingan 3 : 5 dengan total 400 berarti satu bagian = 400 ÷ 8 = 50.\n• Perbandingan berbalik nilai: jumlah pekerja × hari = tetap.\n\nBiasakan membulatkan untuk memperkirakan jawaban lalu pilih opsi yang paling dekat."],
            ['Sinonim & Antonim', "Sinonim adalah kata yang maknanya sama atau mirip, antonim adalah kata yang maknanya berlawanan.\n\n• Pahami kata dasar dan kata serapan: konklusi (kesimpulan), nisbi (relatif), friksi (perpecahan).\n• Pada antonim, pastikan pilihan benar-benar berlawanan, bukan hanya berbeda. Lawan statis adalah dinamis, bukan stabil.\n• Hati-hati dengan pilihan yang maknanya dekat tetapi tidak setara, misalnya efisien (berdaya guna) dengan hemat biaya.\n\nPerbanyak membaca berita dan artikel ilmiah populer untuk memperkaya kosakata."],
            ['Analogi Kata', "Soal analogi meminta Anda menemukan hubungan yang sama antara dua pasang kata.\n\n• Ubah pasangan pertama menjadi kalimat: \"dokter bekerja di rumah sakit\".\n• Terapkan kalimat yang sama pada pilihan: \"guru bekerja di sekolah\".\n• Jenis hubungan yang sering muncul: tempat kerja, alat dan fungsi, penghasil dan hasil, sebab dan akibat, bagian dan keseluruhan.\n• Urutan kata harus sama. PENA : MENULIS tidak sama dengan MENULIS : PENA."],
            ['Silogisme & Penarikan Kesimpulan', "Silogisme adalah penarikan kesimpulan dari dua premis.\n\n• Modus ponens: jika p maka q; p benar, maka q benar.\n• Modus tollens: jika p maka q; q salah, maka p salah.\n• Silogisme: jika p maka q, jika q maka r, maka jika p maka r.\n• Kesalahan umum: dari \"jika p maka q\" dan \"q benar\" tidak bisa disimpulkan p benar.\n• Negasi \"semua\" adalah \"ada yang tidak\"; negasi \"jika p maka q\" adalah \"p dan tidak q\".\n• Kontraposisi \"jika p maka q\" adalah \"jika tidak q maka tidak p\"."],
            ['Pola Huruf & Urutan', "Pola huruf diselesaikan dengan mengubah huruf menjadi angka urutan alfabet (A = 1, B = 2, … Z = 26).\n\n• A, C, E, G menjadi 1, 3, 5, 7 sehingga suku berikutnya 9 = I.\n• Z, X, V, T menjadi 26, 24, 22, 20 sehingga berikutnya 18 = R.\n\nSoal urutan (antrean, tinggi badan, umur) paling mudah diselesaikan dengan menggambar garis dan menempatkan setiap nama sesuai petunjuk satu per satu."],
        ];

        foreach ($materi as [$judul, $isi]) {
            Materi::create(['title' => $judul, 'content' => $isi]);
        }
    }
}
