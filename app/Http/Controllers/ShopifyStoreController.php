<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ShopifyRequest;
use App\Http\Controllers\Controller;
use App\Models\ShopifyStore;
use App\Http\Requests\ShopifyStorePostRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class ShopifyStoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display shopify store register page
     *
     * @param  \Illuminate\Http\Request  $request
     *
     */
    public function create(Request $request)
    {
        return view('register-store');
    }

    /**
     * Display a single product for given store
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id 
     * @param int $product_id
     *
     */
    public function product(Request $request, $id, $product_id)
    {
        $store = ShopifyStore::findOrFail($id);
        if ($store->user_id != Auth::id()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        $prods = new ShopifyRequest($store->id, "/products/$product_id.json", "GET");
        $prods = $prods->execute();
        if (is_array($prods) && array_key_exists("product", $prods)) {
            $prods = $prods['product'];
        } else {
            $prods = [];
        }
        return view('product', [
            "store" => $store,
            "product" => $prods,
        ]);
    }

    /**
     * Saves a product to Shopify
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id 
     * @param int $product_id
     *
     */
    public function save_product(Request $request, $id, $product_id)
    {
        $store = ShopifyStore::findOrFail($id);
        if ($store->user_id != Auth::id()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        $prods = new ShopifyRequest($store->id, "/products/$product_id.json", "GET");
        $prods = $prods->execute();
        if (is_array($prods) && array_key_exists("product", $prods)) {
            $prods = $prods['product'];
            $data =  ["product" => ["id" => $product_id, "title" => $request->title, "body_html" => $request->body_html]];
            $put = new ShopifyRequest($store->id, "/products/" . $product_id . ".json", "PUT", $data);
            $resp =  $put->execute();
            if ($resp === false) {
                throw ValidationException::withMessages([
                    "product_id" => "Failed to save product."
                ]);
            } else {
                return redirect()->route('store-product', ['id' => $id, 'product_id' => $product_id]);
            }
        } else {
            throw ValidationException::withMessages([
                'product_id' => __('Could not validate product in Shopify.'),
            ]);
        }
    }

    /**
     * Display store dashboard
     *
     * @param  \Illuminate\Http\Request  $request
     *
     */
    public function dashboard(Request $request, $id)
    {
        $store = ShopifyStore::findOrFail($id);

        if ($store->user_id != Auth::id()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $prods = new ShopifyRequest($store->id, "/products.json?fields=id,title,image&limit=200", "GET");
        $prods = $prods->execute();
        if (is_array($prods) && array_key_exists("products", $prods)) {
            $prods = $prods['products'];
        } else {
            $prods = [];
        }


        return view('store', [
            "store" => $store,
            "products" => $prods,
            "json" => json_encode($prods)
        ]);
    }

    /**
     * Handle an incoming new shopify store request.
     *
     * @param  \Illuminate\Http\Requests\ShopifyStorePostRequest  $request
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(ShopifyStorePostRequest $request)
    {



        $validated = $request->validated();

        $resp = $request->store();

        if (Str::startsWith(request()->path(), 'api')) {
            return $resp;
        }


        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Handle an incoming new shopify store request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function remove(Request $request)
    {


        $request->validate([
            'shopify_store_id' => ['required', 'integer'],
        ]);


        $store = ShopifyStore::findOrFail($request->shopify_store_id);

        if ($store->user_id != Auth::id()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $store->delete();


        return redirect(RouteServiceProvider::HOME);
    }
}
