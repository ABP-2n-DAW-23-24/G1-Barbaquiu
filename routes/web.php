<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\BarbecuesController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Models\User;

Route::get('/', [IndexController::class, 'show'])->name('index');

Route::get('/test', [TestController::class, 'index'])->name('test');

Route::get('friends', [FriendsController::class, 'index'])->name('friends.index');
Route::delete('friends/{id}', [FriendsController::class, 'destroy'])->name('friends.destroy');

Route::middleware('auth')->group(function () {
    Route::resource('profile', ProfileController::class)->only(['edit', 'update', 'destroy']);
});


// Google login
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
});
Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    $user = User::where('email', $googleUser->email)->first();

    if (!$user) {
        $user = User::create([
            'id' => $googleUser->id,
            'name' => $googleUser->user['given_name'],
            'email' => $googleUser->email,
            'password' => Str::random(16),
        ]);
    }

    // Log the user in
    Auth::login($user);

    return redirect('/');
});

Route::get('/api/user', [UserController::class, 'apiShowLogged']);

Route::resource('barbecues', BarbecuesController::class);

require __DIR__.'/auth.php';


