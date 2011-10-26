<?php 

require('global.php');

// Get the restaurant's ID out of the POST data
$id = $_POST['restaurant'];

// Create an array in the session to hold a list of all the current orders (one for each restaurant that the user browses to)
if (!isset($_SESSION['orders']))
	$_SESSION['orders'] = array();

// Check the orders array for the given restaurant ID...if there's a value, try to use the existing order
if (array_key_exists($id, $_SESSION['orders']) && $_SESSION['orders'][$id])
{
	$order_id = $_SESSION['orders'][$id];
	$order = get_order($order_id);
	
	// If the order is unsubmitted, roll with it...otherwise, make a new order
	if ($order && is_array($order) && (!isset($order['s']) || $order['s'] == 0) && (!isset($order['a']) || $order['a'] == 0))
	{
		// As noted below, the POST parameter names match up to the item fields that the API expects
		// So we can just pass the POST data along to the API
		// If anything is invalid, the API will fail, so there's not much sense in validating this twice
		// The API will just discard any additional data, also
		add_item_to_order($order_id, $_POST);
		
		// Send the user back to the menu page after adding the item
		header('Location: menu.php?id='.$id);
		exit;
	}
} 

// Create a new order in the Open Dining API, with the given item's information (via $_POST)
// Because the POST parameters match up to the item fields that the Orders API expects, we can pass them in directly
$order_id = create_new_order($id, $_POST);

// Save the new order ID in the session for later use
$_SESSION['orders'][$id] = $order_id;
header('Location: menu.php?id='.$id);