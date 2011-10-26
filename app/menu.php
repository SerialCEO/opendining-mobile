<?php 
	require('global.php');
	
	// "id" is the restaurant ID that we want to show the menu for
	$id = $_GET["id"];

	// Get the restaurant's menu information from the Open Dining API (defined in global.php)
	$data = get_menu($id);
	$categories = $data['menu'];
	
	$submit_url = NULL;
	
	// Check the orders array for the given restaurant ID...if there's a value, try to use it
	if (isset($_SESSION['orders']) && array_key_exists($id, $_SESSION['orders']) && $_SESSION['orders'][$id])
	{
		$submit_url = 'https://www.opendining.net/orders/submit/' . $_SESSION['orders'][$id] . '?display=touch';
	}
?>

<div data-role="page"> 
	<div data-role="header" data-theme="b" data-position="fixed">
		<h1><?php echo $data['name']; ?></h1>
		<?php if ($submit_url) { // If we have an order, then show the submit button ?>
		<a href="<?php echo $submit_url; ?>" class="ui-btn-right" rel="external">Cart</a>
		<?php } ?>
	</div> 
	<div data-role="content">
		<ul id="categories" data-role="listview">
			<?php foreach ($categories as $category) { ?>
			<li>
				<?php echo $category['name']; ?>
				<ul>
				<?php if ($category['items']) { foreach ($category['items'] as $item) { ?>
					<li><a href="item.php?id=<?php echo $item['id']; ?>&restaurant=<?php echo $id; ?>"><?php echo $item['name']; ?></a></li>
				<?php } } ?>
				</ul>
			</li>
			<?php } ?>
		</ul>
	</div>
</div> 