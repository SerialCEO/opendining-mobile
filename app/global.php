<?php

// Set your Open Dining API Key here.  See http://opendining.net/developers for more info, or to create an account.
define('API_KEY', 'Your API Key Goes Here');

// You don't need to change anything below this point (but feel free to look around)

session_start();

// Given a restaurant ID, return an array with the restaurant's menu data from the Open Dining API
function get_menu($restaurant_id)
{
	return api_get('restaurants/'.$restaurant_id.'/menu/tier');
}

function get_item($item_id)
{
	return api_get('menuitems/'.$item_id);
}

// Given an order ID, return an array with order information from the Open Dining API
function get_order($order_id)
{
	return api_get('orders/'.$order_id);
}

function add_item_to_order($order_id, $item_data)
{
	$url = 'http://api.opendining.net/orders/'.$order_id.'/items';
	
	// Add the API key to the POST data
	$item_data['key'] = API_KEY;
	
	// Set cURL POST options
	$options = array(
		CURLOPT_POST => TRUE,
		CURLOPT_POSTFIELDS => http_build_query($item_data)
	);
	
	return get($url, $options);
}

function create_new_order($restaurant_id, $item_data)
{
	$url = 'http://api.opendining.net/orders';

	// By wrapping the item parameter in an array and calling it "items" the Orders API will accept this as our first item
	$data = array('restaurant_id' => $restaurant_id, 'key' => API_KEY, 'items' => array($item_data));
	
	// Set cURL POST options
	$options = array(
		CURLOPT_POST => TRUE,
		CURLOPT_POSTFIELDS => http_build_query($data)
	);
	
	return get($url, $options);
}

// Issue a GET call to the Open Dining API
function api_get($endpoint)
{
	$url = 'http://api.opendining.net/'.$endpoint.'?key='.API_KEY;
	$json = get($url);
	return json_decode($json, TRUE);
}

// Issue an HTTP request via cURL
function get($url, array $options = NULL)
{
    if ($options === NULL)
        $options = array();
 
    $options[CURLOPT_USERAGENT] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1";
    $options[CURLOPT_RETURNTRANSFER] = TRUE;
 
    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
 
    if ($code && $code < 200 || $code > 299)
    {
        $error = $response;
    }
    elseif ($response === FALSE)
    {
        $error = curl_error($ch);
    }
 
    curl_close($ch);
 
    if (isset($error))
        return NULL; // If this is happening, you might want to trap the error here and inspect it
 
    return $response;
}