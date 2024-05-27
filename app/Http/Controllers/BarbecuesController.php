<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;
use App\Models\Barbecue;
use App\Models\User;
use App\Models\Basket;
use App\Models\BasketProduct;
use App\Models\Product;
use App\Models\BarbecueFriendship;
use App\Models\Review;


class BarbecuesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Barbecues/Index');
    }

    public function apiIndex(Request $request)
    {
        $limit = $request->input('limit');
        $offset = $request->input('offset');

        $order = $request->input('order', 'desc');
        $time = $request->input('time', 'forever');
        $search = $request->input('search');

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $distance = $request->input('distance');

        // get the barbecue with user and images
        $barbecues = Barbecue::with('user', 'images', 'comments', 'friendships');

        if ($limit) {
            $barbecues = $barbecues->limit($limit);
        }

        if ($offset) {
            $barbecues = $barbecues->offset($offset);
        }

        if ($order === 'asc') {
            $barbecues = $barbecues->orderBy('created_at', 'asc');
        } else {
            $barbecues = $barbecues->orderBy('created_at', 'desc');
        }

        if ($latitude && $longitude) {
            $barbecues = $barbecues->selectRaw('*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude]);

            if ($distance) {
                $barbecues = $barbecues->having('distance', '<', $distance);
            }

            $barbecues = $barbecues->orderBy('distance', 'asc');
        }

        if ($time === 'week') {
            $barbecues = $barbecues->where('created_at', '>=', now()->subWeek());
        } elseif ($time === 'month') {
            $barbecues = $barbecues->where('created_at', '>=', now()->subMonth());
        } elseif ($time === 'semester') {
            $barbecues = $barbecues->where('created_at', '>=', now()->subMonths(6));
        }

        if ($search) {
            $barbecues = $barbecues->where('title', 'like', "%$search%");
        }

        return response()->json($barbecues->get());
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
        $user = auth()->user();
        $barbecue = $user->barbecues()->create($request->all());
        
       


        return response()->json($barbecue);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        $friends = $user->friends()->get();
        $barbecue = Barbecue::findOrFail($id);
        $barbecue = Barbecue::with('basket')
        ->with('basket.basketProduct')
        ->with('basket.basketProduct.user')
        ->with('basket.basketProduct.product')
        ->with('members')
        ->with('friendships')
        ->with('images')
        ->with('messages')
        ->find($id);
       
        $members = $barbecue->members()->get();

        foreach ($members as $member) {
            $friends = $friends->reject(function ($friend) use ($member) {
                return $friend->id === $member->id;
            });
        }
        
        return Inertia::render('Barbecues/Show', [
            'barbecue' => $barbecue,
            'friends' => $friends,
            'members' => $members,
        ]);
    }

    public function apiMyBarbecues() {
        $user = auth()->user();
        
        $joinedBarbecues = $user->joinedBarbecues()->get()->toArray();
        
        $organizedBarbecues = Barbecue::where('user_id', $user->id)->get()->toArray();
        
        $barbecues = [];
    
        foreach ($joinedBarbecues as $relationship) {
            $barbecue = $relationship['barbecue'];
            $barbecue['user'] = User::find($barbecue['user_id'])->toArray();
            $barbecues[] = $barbecue;
        }
    
        foreach ($organizedBarbecues as $barbecue) {
            $barbecue['user'] = $user->toArray();
            $barbecues[] = $barbecue;
        }
    
        return response()->json($barbecues);
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $barbecue = Barbecue::where('id', $id)->with('images')->first();
        $user = auth()->user();
        if ($barbecue->user_id !== $user->id) {
            return redirect()->route('index')->withErrors(['error' => 'No tienes permiso para editar esta barbacoa.']);
        }
        
        $friends = $user->friends()->get();
        
        $users = User::whereNotIn('id', $friends->pluck('id'))
                ->where('id', '!=', $user->id)
                ->inRandomOrder()
                ->get();
    
        for ($i = 0; $i < count($users); $i++) {
            $friendStatus = "none";
            if ($user->isFriendWith($users[$i])) {
                $friendStatus = 'friend';
            } else if ($user->alreadySentFriendRequestTo($users[$i])) {
                $friendStatus = 'sent';
            } else if ($user->alreadyHasFriendRequestFrom($users[$i])) {
                $friendStatus = 'received';
            } 
            $users[$i]->friendStatus = $friendStatus;
        }
    
        $filteredUsers = [];
        foreach ($users as $user) {
            if ($user->friendStatus === 'none') {
                $filteredUsers[] = $user;
            }
        }
        
        return Inertia::render('Barbecues/Edit', [
            'barbecue' => $barbecue,
            'filteredUsers' => $filteredUsers,
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $barbecue = Barbecue::findOrFail($id);
        $barbecue->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
            
        $barbecue = Barbecue::findOrFail($id);
        $barbecue->delete();
        return response()->json($barbecue);
        
    }

    /**
     * Send invitation to join barbecue.
     */
    public function sendInvitation(Request $request, string $id)
    {
        $barbecue = Barbecue::findOrFail($id); // ok
        $guest = User::findOrFail($request->user_id);
        
        // get logged user
        $user = User::findOrFail(auth()->user()->id);

        $barbecue->sendInvitation($guest, $user);

        return redirect()->route('barbecues.show', ['barbecue' => $id]);
    }


    /**
     * Send barbecue join request.
     */
    public function sendBarbecueJoinRequest(Request $request, string $id)
    {
        $user = auth()->user();
        $barbecue = Barbecue::findOrFail($id);
        $user->sendBarbecueJoinRequest($barbecue);

        return redirect()->route('barbecues.show', ['barbecue' => $id]);
    }

    /**
     * Destroy frienship by user id and barbecue id.
     */
    public function destroyFriendship(Request $request) {
        $barbecue = Barbecue::findOrFail($request->barbecue_id);
        $user = User::findOrFail($request->user_id);

        $friendship = BarbecueFriendship::where('barbecue_id', $barbecue->id)->where('guest_id', $user->id)->firstOrFail();
        $friendship->delete();

        return response()->json(["ok" => true]);
    }

    public function rejectBarbecueJoinRequest(Request $request, string $barbecueId, string $userId)
    {
        $barbecue = Barbecue::findOrFail($barbecueId);
        $user = User::findOrFail($userId);
        
        $friendship = BarbecueFriendship::where('barbecue_id', $barbecue->id)->where('user_id', $user->id)->firstOrFail();
        
        $friendship->delete();

        return response()->json([$barbecueId, $userId]);
    }

    public function acceptBarbecueInvitation(Request $request, string $barbecueId, string $userId)
    {
        $barbecue = Barbecue::findOrFail($barbecueId);
        $user = User::findOrFail($userId);


        $friendship = BarbecueFriendship::where('barbecue_id', $barbecue->id)->where('guest_id', $user->id)->firstOrFail();

        $friendship->accepted = true;

        $friendship->save();


        return response()->json($friendship);
    }

    public function acceptBarbecueJoinRequest(Request $request, string $barbecueId, string $userId)
    {
        $barbecue = Barbecue::findOrFail($barbecueId);
        $user = User::findOrFail($userId);

        $friendship = BarbecueFriendship::where('barbecue_id', $barbecue->id)->where('user_id', $user->id)->firstOrFail();

        $friendship->accepted = true;

        $friendship->save();

        return response()->json($friendship);
    }

    

    /**
     * Add new product to product list, basket_product and basket.
     */
    public function addProduct(Request $request, string $id)
    {
        $request = $request->all();
        $user = auth()->user();
        $barbecue = Barbecue::findOrFail($id);
        
        $product = Product::firstOrCreate([
            'user_id' => $user->id,
            'name' => $request['product_name'],
            'price' => $request['product_price'],
            'is_deleted' => false
        ]);
        
        $basket = Basket::firstOrCreate(['barbecue_id' => $barbecue->id]);
        
        $existingBasketProduct = BasketProduct::where('basket_id', $basket->id)
                                            ->where('product_id', $product->id)
                                            ->first();
        
        if ($existingBasketProduct) {
            $existingBasketProduct->quantity += 1;
            $existingBasketProduct->save();
            $basketProduct = $existingBasketProduct;
        } else {
            $basketProduct = BasketProduct::create([
                'user_id' => $user->id,
                'basket_id' => $basket->id,
                'product_id' => $product->id,
                'price' => $request['product_price'],
                'quantity' => 1
            ]);
        }
        
        return response()->json($basketProduct);
    }
    
    public function minusProduct(Request $request, string $id)
    {
        $request = $request->all();
        $user = auth()->user();
        $barbecue = Barbecue::findOrFail($id);
        $product = Product::findOrFail($request['product_id']);
        $basket = Basket::firstOrCreate(['barbecue_id' => $barbecue->id]);
        $basketProduct = BasketProduct::where('basket_id', $basket->id)
                                        ->where('product_id', $product->id)
                                        ->first();
        $basketProduct->quantity -= 1;
        if ($basketProduct->quantity === 0) {
            $basketProduct->delete();
            return response()->json(['deleted' => true]);
        } else {
            $basketProduct->save();
            return response()->json($basketProduct);
        }
    }

    /**
     * Assign product to user.
     */

     public function assignProduct(Request $request, string $id)
     {
         $data = $request->all();
         $memberId = $data['member_id'];
         $barbecue = Barbecue::findOrFail($id);
         $product = Product::findOrFail($data['product_id']);
         $basket = Basket::firstOrCreate(['barbecue_id' => $barbecue->id]);
         $basketProduct = BasketProduct::where('basket_id', $basket->id)
                                         ->where('product_id', $product->id)
                                         ->first();
         $basketProduct->user_id = $memberId;
         $basketProduct->save();
     }

     /**
     * Make a review for barbecue, with rating, comment, user_id (who recives de review), guest_id (who do the review) and barbecue_id.
     */
    public function review(Request $request)
    {
        $data = $request->all();
        $user = auth()->user();
        $barbecue = Barbecue::findOrFail($data['barbecue_id']);
        $review = Review::create([
            'rating' => $data['rating'],
            'content' => $data['content'],
            'user_id' => $barbecue->user_id,
            'guest_id' => $user->id,
            'barbecue_id' => $barbecue->id
        ]);
        
    }
}
