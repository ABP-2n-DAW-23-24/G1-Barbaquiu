<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Friendship;

class FriendsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $friends = $user->friends()->get();

        return Inertia::render('Friends/Index',[
            'friends' => $friends,
        ]);
        
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

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
public function destroy($friend_id)
{
    $user = auth()->user();
    $friendship = Friendship::where('friend_id', $user->id)
                             ->where('user_id', $friend_id)
                             ->first();

    if ($friendship) {
        $friendship->delete();
        return redirect()->route('friends.index')->with('success', 'Amistad eliminada exitosamente');
    } else {
        return redirect()->route('friends.index')->with('error', 'No se encontró la amistad para eliminar');
    }
}
}