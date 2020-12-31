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
        $data = Images::where('author', '%'.$term.'%', 'LIKE')
            ->orWhere('camera', '%'.$term.'%', 'LIKE')
            ->orWhere('tags', '%'.$term.'%', 'LIKE')
            ->paginate();

        return response()->json($data);
    }
}
