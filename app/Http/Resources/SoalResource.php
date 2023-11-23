<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SoalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pertanyaan' => $this->pertanyaan,
            'kategori' => $this->kategori,
            'jawaban_a' => $this->jawaban_a,
            'jawaban_b' => $this->jawaban_b,
            'jawaban_c' => $this->jawaban_c,
            'jawaban_d' => $this->jawaban_d,
        ];
    }
}
