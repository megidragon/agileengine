<?php

namespace App\Http\Controllers\Images;

use App\Models\Images;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    public function index()
    {
        return response()->json(Images::select(['id', 'cropped_picture'])->paginate());
    }

    public function show($id)
    {
        return response()->json(Images::findOrFail($id));
    }

    public function search($term)
    {
        $data = Images::where(function ($query) use ($term) {
                return $query->where('author', 'LIKE', '%' . $term . '%')
                    ->orWhere('remote_id', 'LIKE', '%'.$term.'%')
                    ->orWhere('camera', 'LIKE', '%'.$term.'%')
                    ->orWhere('tags', 'LIKE', '%'.$term.'%');
            })
            ->paginate();

        return response()->json($data);
    }
}
