<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ShopifyStore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Providers\RouteServiceProvider;


class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Show the dashboard for the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $id = Auth::id(); // Retrieve the currently authenticated user's ID...
        
        return view('dashboard', [
            'user' => User::findOrFail($id),
            'stores' => ShopifyStore::where('user_id', $id)->get()
        ]);
    }


    public function get_stores() {
        $id = Auth::id(); // Retrieve the currently authenticated user's ID...
        
        return ["stores" =>  ShopifyStore::where('user_id', $id)->get()];
    }

    /**
     * Refresh the token for authenticated user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refresh_token() {
        $id = Auth::id(); // Retrieve the currently authenticated user's ID...

        User::findOrFail($id)
        ->update(['api_token' => Str::random(80)]);
        return redirect(RouteServiceProvider::HOME);

    }

}

?>