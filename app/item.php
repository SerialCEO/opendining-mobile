<?php

require('global.php');

$id = $_GET["id"]; // The menu item ID
$restaurant = $_GET["restaurant"]; // The restaurant ID

// Get the item's data from the Open Dining API (defined in global.php)
$item = get_item($id);

?>
<div data-role="page" data-theme="b"> 
	<div data-role="header">
		<h1><?php echo $item['name']; ?></h1>
	</div> 
	<div data-role="content">
		<h2 class="item-name"><?php echo $item['name']; ?></h2>
		<form class="additem" method="post" action="order.php">
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="hidden" name="restaurant" value="<?php echo $restaurant ?>" />
			<?php if (count($item['prices']) == 1) { ?>
				<p>
					Price: $<?php echo money_format("%!i", $item['prices'][0]['price']); ?>
				</p>
			<?php } ?>
			
			<?php if (isset($item['description']) && $item['description']) { ?>
			<p>
				<?php echo $item['description'] ?>
			</p>
			<?php } ?>
				
			<div class="options">
				<?php if (count($item['prices']) > 1) { ?>        
				<div data-role="fieldcontain">
					<fieldset data-role="controlgroup">
						<legend>Size</legend>
						<?php
						$checked = "checked";
						foreach ($item['prices'] as $price) { ?>
						<input type="radio" name="size" id="price_<?php echo $item['id'] ?>_<?php echo $price['name'] ?>" value="<?php echo $price['name'] ?>" <?php echo $checked ?> />
						<label for="price_<?php echo $item['id'] ?>_<?php echo $price['name'] ?>">
							<?php echo $price['name'] ?> (<?php echo "$".money_format("%!i", $price['price']) ?>)
						</label>
						<?php
							$checked = "";
						} 
						?>
					</fieldset>
				</div>
				<?php }?>            

				<?php if (array_key_exists('option_groups', $item) && is_array($item['option_groups']) && count($item['option_groups']) > 0) {
						foreach ($item['option_groups'] as $option_group) {
					?>

				<div data-role="fieldcontain" class="option <?php if (array_key_exists('required', $option_group) && $option_group['required']) echo "required" ?>">
					<fieldset data-role="controlgroup">
						<legend><?php echo $option_group['name'] ?></legend>
					<?php
					if (!isset($option_group['multiselect']) || $option_group['multiselect']) {
						$type = "checkbox";
						$checked = "";
					} else {
						$checked = "checked";
						$type = "radio";
					}
					$option_num = 0;
					foreach ($option_group['options'] as $option) { ?>
						<input type="<?php echo $type ?>" name="options[<?php echo $option_group['name'] ?>][]" id="<?php echo $option_group['name'] ?>_<?php echo $item['id'] ?>_<?php echo $option_num; ?>" value="<?php echo $option['name'] ?>" <?php echo $checked ?> />
						<label for="<?php echo $option_group['name'] ?>_<?php echo $item['id'] ?>_<?php echo $option_num++; ?>">
							<?php echo $option['name'] ?>
							<?php if ($option['price'] && $option['price'] > 0) echo " ($" . money_format("%!i", $option['price']) . ")"; ?>
						</label>
					<?php
						$checked = "";
					} ?>
					</fieldset>
				</div>

				<?php } } ?>
			</div>
			
			<div data-role="fieldcontain">
				<label for="notes">Special Instructions</label>
				<textarea cols="40" rows="3" name="notes" id="notes"></textarea>
			</div>
			
			<button type="submit">Add to Order</button>
		</form>
		
	</div>
</div> 