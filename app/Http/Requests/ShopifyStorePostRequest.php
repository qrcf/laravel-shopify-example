<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ShopifyStore;
use App\Helpers\ShopifyRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class ShopifyStorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'shopify_url' => 'required', 'string', 'max:255',
            'shopify_api_key' => 'required', 'string', 'max:255',
            'shopify_api_token' => 'required', 'string', 'max:255'
        ];
    }

    // public function messages() {
    //     return [
    //         'shopify_url.exists' => 'This store already exists in the system.'
    //     ];
    // }

    public function store()
    {
        $found = ShopifyStore::where('shopify_url', $this->shopify_url)->get();
        if (count($found) > 0) {
            throw ValidationException::withMessages([
                'shopify_url' => 'This store already exists in the system.'
            ]);
            return false;
        }

        $url = trim($this->shopify_url);
        $url = str_replace("https://", "", $url);
        $url = str_replace("http://", "", $url);
        $url = explode("/", $url)[0];

        $url_parts = explode(".", $url);

        if (!is_array($url_parts) || count($url_parts) != 3) {
            throw ValidationException::withMessages([
                'shopify_url' => 'Wrong number of url parts to myshopify domain.',
            ]);
        } else {
            if ($url_parts[1] != "myshopify") {
                throw ValidationException::withMessages([
                    'shopify_url' => 'Missing myshopify in domain.'
                ]);
            }
        }

        $validation = ShopifyRequest::validate_token($url, $this->shopify_api_key, $this->shopify_api_token);

        if ($validation != false) {
            $store = ShopifyStore::create([
                'user_id' => Auth::id(),
                'name' => $validation['shop']['name'],
                'shopify_url' => $url,
                'api_key' => $this->shopify_api_key,
                'api_token' => $this->shopify_api_token,
            ]);
            return $store;
        } else {
            throw ValidationException::withMessages([
                'shopify_url' => __('Could not validate shopify URL with api key and token.'),
            ]);
            return false;
        }
    }
}
