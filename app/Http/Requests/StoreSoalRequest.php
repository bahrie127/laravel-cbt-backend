<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pertanyaan' => 'required|string|max:2000',
            'kategori' => 'required|in:Numeric,Verbal,Logika',
            'jawaban_a' => 'required|string|max:255',
            'jawaban_b' => 'required|string|max:255',
            'jawaban_c' => 'required|string|max:255',
            'jawaban_d' => 'required|string|max:255',
            'kunci' => 'required|in:a,b,c,d',
        ];
    }

    public function attributes(): array
    {
        return [
            'jawaban_a' => 'pilihan A',
            'jawaban_b' => 'pilihan B',
            'jawaban_c' => 'pilihan C',
            'jawaban_d' => 'pilihan D',
            'kunci' => 'kunci jawaban',
        ];
    }
}
