<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SoalResource;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\UjianSoalList;
use Illuminate\Http\Request;

class UjianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    //create ujian
    public function createUjian(Request $request)
    {
        // get 20 soal angka random unique

        $soalAngka = Soal::where('kategori', 'Numeric')->inRandomOrder()->limit(20)->get();
        // get 20 soal verbal random
        $soalVerbal = Soal::where('kategori', 'Verbal')->inRandomOrder()->limit(20)->get();
        // get 20 soal logika random
        $soalLogika = Soal::where('kategori', 'Logika')->inRandomOrder()->limit(20)->get();

        //create ujian
        $ujian = Ujian::create([
            'user_id' => $request->user()->id,
        ]);

        foreach ($soalAngka as $soal) {
            UjianSoalList::create([
                'ujian_id' => $ujian->id,
                'soal_id' => $soal->id,
            ]);
        }
        foreach ($soalVerbal as $soal) {
            UjianSoalList::create([
                'ujian_id' => $ujian->id,
                'soal_id' => $soal->id,
            ]);
        }

        foreach ($soalLogika as $soal) {
            UjianSoalList::create([
                'ujian_id' => $ujian->id,
                'soal_id' => $soal->id,
            ]);
        }

        return response()->json([
            'message' => 'Ujian berhasil dibuat',
            'data' => $ujian,
        ]);
    }

    //get list soal by kategori
    public function getListSoalByKategori(Request $request)
    {
        $ujian = Ujian::where('user_id', $request->user()->id)->first();
        if (!$ujian) {
            return response()->json([
                'message' => 'Ujian tidak ditemukan',
                'data' => [],
            ], 200);
        }
        $ujianSoalList = UjianSoalList::where('ujian_id', $ujian->id)->get();
        $soalIds = $ujianSoalList->pluck('soal_id');

        // dd($soalIds);

        $soal = Soal::whereIn('id', $soalIds)->where('kategori', $request->kategori)->get();

        return response()->json([
            'message' => 'Berhasil mendapatkan soal',
            'data' => SoalResource::collection($soal),
        ]);
    }

    //jawab soal
    public function jawabSoal(Request $request)
    {
        $validatedData = $request->validate([
            'soal_id' => 'required',
            'jawaban' => 'required'
        ]);


        $ujian = Ujian::where('user_id', $request->user()->id)->first();
        //if ujian not found return empty
        if (!$ujian) {
            return response()->json([
                'message' => 'Ujian tidak ditemukan',
                'data' => [],
            ], 200);
        }
        $ujianSoalList = UjianSoalList::where('ujian_id', $ujian->id)->where('soal_id', $validatedData['soal_id'])->first();
        $soal = Soal::where('id', $validatedData['soal_id'])->first();

        //cek jawaban
        if ($soal->kunci == $validatedData['jawaban']) {
            //  $ujianSoalList->kebenaran = true;
            $ujianSoalList->update(
                [
                    'kebenaran' => true
                ]
            );
        } else {
            // $ujianSoalList->kebenaran = false;
            $ujianSoalList->update(
                [
                    'kebenaran' => false
                ]
            );
        }

        return response()->json([
            'message' => 'Berhasil simpan jawaban',
            'jawaban' => $ujianSoalList->kebenaran,
        ], 200);
    }

    //hitung nilai ujian by kategori
    public function hitungNilaiUjianByKategori(Request $request)
    {
        $kategori = $request->kategori;
        $ujian = Ujian::where('user_id', $request->user()->id)->first();
        if (!$ujian) {
            return response()->json([
                'message' => 'Ujian tidak ditemukan',
                'data' => [],
            ], 200);
        }
        $ujianSoalList = UjianSoalList::where('ujian_id', $ujian->id)->get();
        //ujiansoallist by kategori
        $ujianSoalList = $ujianSoalList->filter(function ($value, $key) use ($kategori) {
            return $value->soal->kategori == $kategori;
        });

        //hitung nilai
        $totalBenar = $ujianSoalList->where('kebenaran', true)->count();
        $totalSoal = $ujianSoalList->count();
        $nilai = ($totalBenar / $totalSoal) * 100;

        $kategori_field = 'nilai_verbal';
        if ($kategori == 'Numeric') {
            $kategori_field = 'nilai_angka';
        } else if ($kategori == 'Logika') {
            $kategori_field = 'nilai_logika';
        }

        //update nilai
        $ujian->update([
            $kategori_field => $nilai
        ]);

        return response()->json([
            'message' => 'Berhasil mendapatkan nilai',
            'nilai' => $nilai,
        ], 200);
    }
}
