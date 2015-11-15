<?php
	// print ingredients for entire recipe, with common items summed
	$checkList = [];
	for ($i = 0; $i < count($recipe["ingredients"]); $i ++) {
		if (isset($recipe["ingredients"][$i])) {
			$ingredient =[];
			$ingredient["name"] = $recipe["ingredients"][$i];
			$ingredient["quantities"] = [];
			$ingredient["units"] = [];
			// extract ingredient quantities from dish ingredients and save to multidimensional array $checkList
			foreach ($recipe["dishes"] as $dish) {
				$dishIngredients = parseDishIngredients($dish);
				foreach ($dishIngredients as $dishIngredient) {
					if ((int)$dishIngredient["id"] == $i + 1) {
						$ingredient["quantities"][] = $dishIngredient["quantity"] ? (float)$dishIngredient["quantity"] : null;
						$ingredient["units"][] = $dishIngredient["unit"] ? $dishIngredient["unit"] : null;
					}
				}
			}
			$checkList[] = $ingredient;
		}
	}
	// sort list alphabtically
?>
<div class="page" id="checklist">
	<div class="list-count">
		<div class="counter"><?= count($recipe["ingredients"]) ?></div>
		<h2><?= count($recipe["ingredients"]) == 1? "item": "items" ?> remaining
		</h2>
	</div>
	<ul class="checklist">
	<?php
		// print out list items
		foreach ($checkList as $item):
			// determine whether one item (eg one tomato) is required in list
			$uniqueUnits = array_unique($item["units"]);
			$single = (count(array_unique($item["units"])) == 1 and reset($uniqueUnits) === null and ceil(array_sum($item["quantities"])) == 1);
			$name = ucfirst(formatString($item["name"], !$single and array_unique($item["units"]) !== null));
			// print quantities and units, if required
			$unitList = []; // array for storing strings of quantity and unit pairs, eg "400 g"
			if (array_sum($item["quantities"]) > 0) {
				// loop through all used units without dupliactes
				foreach (array_unique($item["units"]) as $uniqueUnit) {
					$quantityList = []; // array for storing all quantities of a particular unit, to be summed
					// loop through all units and match with selected unique unit
					foreach ($item["units"] as $index => $unit) {
						if ($unit == $uniqueUnit)
							$quantityList[] = $item["quantities"][$index]; // append quantity to array
					}
					// append formated quantity string to array
					$unitList[] = formatNumber(array_sum($quantityList)) . ($uniqueUnit !== null? "&nbsp;" . formatString($uniqueUnit, array_sum($quantityList) > 1) : "");
				}
			} ?>
			<li><?= $name ?> <span class="info"><?= !$single ? implode($unitList, " + ") : "" ?></li>
		<?php endforeach ?>
	</ul>
</div>
