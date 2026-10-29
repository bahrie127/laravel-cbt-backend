<?php

return [

    /*
    | Kategori tes TPA. Kunci = nilai kolom soals.kategori (dipakai app), field = akhiran kolom
    | di tabel ujians (nilai_angka, status_angka, timer_angka, mulai_angka_at).
    */
    'kategori' => [
        'Numeric' => ['field' => 'angka', 'label' => 'Tes Angka'],
        'Verbal' => ['field' => 'verbal', 'label' => 'Tes Verbal'],
        'Logika' => ['field' => 'logika', 'label' => 'Tes Logika'],
    ],

    // Jumlah soal acak per kategori untuk setiap ujian.
    'jumlah_soal' => (int) env('CBT_JUMLAH_SOAL', 20),

    // Durasi per kategori (menit), dihitung server sejak soal kategori itu pertama dibuka.
    'durasi_menit' => (int) env('CBT_DURASI_MENIT', 30),

    // Toleransi jaringan (detik) untuk jawaban yang terkirim tepat saat waktu habis.
    'toleransi_detik' => 5,

    // Rata-rata nilai minimal untuk dinyatakan "Lulus".
    'nilai_lulus' => (int) env('CBT_NILAI_LULUS', 60),
];
