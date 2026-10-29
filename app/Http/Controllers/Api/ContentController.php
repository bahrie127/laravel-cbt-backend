<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /** Semua data, atau satu data lewat ?id= (dipakai app). */
    public function index(Request $request): JsonResponse
    {
        $contents = Content::query()
            ->when($request->id, fn ($query, $id) => $query->where('id', $id))
            ->orderBy('id')
            ->get()
            ->map(fn (Content $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'content' => $item->content,
                'image' => $item->image_url,
                'created_at' => $item->created_at?->toIso8601String(),
                'updated_at' => $item->updated_at?->toIso8601String(),
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $contents,
        ]);
    }
}
