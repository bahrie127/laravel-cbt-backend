<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'image'];

    /** URL absolut gambar (unggahan di disk public atau URL penuh), null bila tidak ada. */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->image) {
                return null;
            }

            return Str::startsWith($this->image, ['http://', 'https://'])
                ? $this->image
                : Storage::disk('public')->url($this->image);
        });
    }
}
