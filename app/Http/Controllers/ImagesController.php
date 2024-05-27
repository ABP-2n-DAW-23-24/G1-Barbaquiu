<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Image;

class ImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $barbecueId)
    {
        $images = $request->file('images');

        foreach ($images as $image) {
            $name = $image->getClientOriginalName();

            $imageModal = new Image();

            $imageName = time() . '_' . $name;

            $path = $image->storeAs('public/assets/img/barbecues/' . $barbecueId . '/' , $imageName);

            $imageModal->barbecue_id = $barbecueId;
            $imageModal->path = '/storage/assets/img/barbecues/' . $barbecueId . '/' . $imageName;

            $imageModal->save();
        }

        return response()->json(['message' => 'Imatges pujades amb èxit'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
    public function destroy($barbecueId, $imageId)
    {
        $image = Image::where('id', $imageId)->where('barbecue_id', $barbecueId)->first();

        if (!$image) {
            return response()->json(['message' => 'Imatge no trobada'], 404);
        }

        $imagePath = 'public/assets/img/barbecues/' . $barbecueId . '/' . basename($image->path);
        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);
        } else {
            return response()->json(['message' => 'Fitxer no trobat a l\'emmagatzematge'], 404);
        }

        $image->delete();

        return response()->json(['message' => 'Imatge eliminada amb èxit'], 200);
    }
}