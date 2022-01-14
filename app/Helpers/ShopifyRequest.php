<?php

namespace App\Helpers;

use App\Models\ShopifyStore;

class ShopifyRequest
{

    public $status = false;
    protected $return_data = false;
    protected $url;
    protected $method;
    protected $data;
    private $prefix = false;
    private static $api_prefix = "/admin/api/2021-10";

    private $next = false;

    public static function validate_token($store, $api_key, $api_token, $fields = "name")
    {

        $url = "https://" . $api_key . ":" . $api_token . "@" . $store . self::$api_prefix . "/shop.json?fields=$fields";


        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        curl_setopt($ch, CURLOPT_TIMEOUT, 900);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);


        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        $headers = self::get_headers_from_curl_response($return);

        $return = substr($return, $header_size);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        $response = json_decode($return, true);
        // return $url;
        if (is_array($response) && array_key_exists("shop", $response)) {
            return $response;
        } else
            return false;
    }

    public function __construct($store_id, $url, $method, $data = false, $batch = false)
    {
        // set defaults

        $store = ShopifyStore::findOrFail($store_id);

        $this->prefix = "https://" . $store->api_key . ":" . $store->api_token . "@" . $store->shopify_url;

        $prefix = $this->prefix;

        if (strstr($url, "/admin/api/")) {
            $this->url = $prefix . $url;
        } else {
            $this->url = $prefix . self::$api_prefix . $url;
        }
        $this->method = $method;
        $this->data = $data;
        return $this;
    }

    public static function set_metafield($store_id, $url, $key, $namespace, $value, $value_type = "json_string")
    {

        $get = new ShopifyRequest($store_id, $url . "?namespace=$namespace&key=$key", "GET");
        $meta = $get->execute();
        $post = false;
        if (is_array($meta) && array_key_exists("metafields", $meta)) {
            if (count($meta['metafields']) > 0) {
                $metafield = $meta['metafields'][0];


                $put = new ShopifyRequest("/admin/api/2021-01/metafields/" . $metafield['id'] . ".json", "PUT", ["metafield" => ["id" => $metafield['id'], "value" => $value]]);
                return $put->execute();
            } else {
                $post = true;
            }
        } else {
            $post = true;
        }

        if ($post !== false) {

            $put = new ShopifyRequest($url, "POST", ["metafield" => ["value_type" => $value_type, "key" => "$key", "namespace" => "$namespace", "value" => $value]]);
            return $put->execute();
        }
    }

    public function execute_paginated($identifier)
    {

        if (strstr($this->url, "?")) {
            $this->url .= "&limit=200";
        } else {
            $this->url .= "?limit=200";
        }

        $response = $this->execute();
        if (array_key_exists($identifier, $response) && count($response[$identifier]) > 0) {
            $return = $response[$identifier];
        } else {
            return false;
        }

        $this->next = $this->shopify_get_next_url($response);

        while ($this->next !== false) {

            $response_new = $this->execute($this->prefix . "/admin/" . $this->next);
            $this->next = $this->shopify_get_next_url($response_new);
            if (array_key_exists($identifier, $response_new) && count($response_new[$identifier]) > 0) {
                $return = array_merge($return, $response_new[$identifier]);
            }
        }

        return $return;
    }

    public function execute_next($identifier, $next = false)
    {

        if (strstr($this->url, "limit") === false) {
            if (strstr($this->url, "?")) {
                $this->url .= "&limit=200";
            } else {
                $this->url .= "?limit=200";
            }
        }

        if ($next !== false && $next !== true) {
            $this->next = $next;
        }

        if ($this->next === false) {

            $response = $this->execute();
            if (array_key_exists($identifier, $response) && count($response[$identifier]) > 0) {
                $return = $response[$identifier];
            } else {
                return false;
            }
            $this->next = $this->shopify_get_next_url($response);

            if ($next !== false) {
                return ["next" => $this->next, "$identifier" => $return];
            } else
                return $return;
        } else {

            $response_new = $this->execute($this->prefix . "/admin/" . $this->next);
            $this->next = $this->shopify_get_next_url($response_new);
            if (array_key_exists($identifier, $response_new) && count($response_new[$identifier]) > 0) {
                $return = $response_new[$identifier];
            } else {
                return false;
            }
            if ($next !== false) {
                return ["next" => $this->next, "$identifier" => $return];
            } else
                return $return;
        }
    }

    public function execute($url = false)
    {

        $response = $this->execute_request($url);

        if (is_bool($response)) {
            $this->status = $response;
        } else {
            $this->status = true;
        }
        return $response;
    }

    private function execute_request($url = false)
    {

        if ($url === false) {
            $url = $this->url;
        }

        if ($this->method == "GET" && is_array($this->data) && count($this->data) > 0) {

            $url = $url . "?" . http_build_query($this->data);
        }

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        curl_setopt($ch, CURLOPT_TIMEOUT, 900);
        if ($this->method != "GET") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
            if ($this->data === false) {
                $this->data = array();
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->data));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        $headers = self::get_headers_from_curl_response($return);

        $return = substr($return, $header_size);

        $previous = false;
        $next = false;

        if (array_key_exists("Link", $headers)) {


            if (preg_match("/<(.[^;]*)>; rel=\"next\"/", $headers['Link'], $next)) {
                $next = $next[1];
            } else {
                $next = false;
            }

            if (preg_match("/<(.[^;]*)>; rel=\"previous\"/", $headers['Link'], $previous)) {
                $previous = $previous[1];
            } else {
                $previous = false;
            }
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($this->method === "POST") {

            if ($http_code != 201 && $http_code != "201") {
                $this->return_data = $return;
                return false;
            } else {
                $this->return_data = json_decode($return, true);
            }
        }

        if ($this->method  === "PUT") {
            if ($http_code == 200 || $http_code == "200" || $http_code == "201" || $http_code == 201) {
                $this->return_data = json_decode($return, true);
                return true;
            } else {
                $this->return_data = $return;

                return false;
            }
        }

        curl_close($ch);
        if ($this->method  === "DELETE") {
            if ($http_code == 200 || $http_code == "200") {
                return true;
            } else {

                return false;
            }
        } else {

            $response = json_decode($return, true);

            if ($response === false) {
                print_r($return);
            }

            if (!is_array($response)) {
                print_r($return);
            }
        }
        $response['next'] = $next;
        $response['previous'] = $previous;

        return $response;
    }

    public function get_return()
    {

        if ($this->return_data !== false) {
            return $this->return_data;
        } else {
            return false;
        }
    }

    private static function get_headers_from_curl_response($response)
    {
        $headers = array();

        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line)
            if ($i === 0)
                $headers['http_code'] = $line;
            else {
                list($key, $value) = explode(': ', $line);

                $headers[$key] = $value;
            }

        return $headers;
    }


    private function shopify_get_next_url($response)
    {
        if (!is_array($response) || $response === false) {
            return false;
        }

        if (array_key_exists("next", $response) && $response['next'] !== false) {
            $next = $response['next'];

            $next = explode("/admin/", $next);
            $next = $next[1];

            return $next;
        } else {
            return false;
        }
    }
}
