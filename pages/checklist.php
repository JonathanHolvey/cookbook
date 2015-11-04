<div class="page" id="checklist">
	<div class="list-count">
		<div class="counter">
			<?php echo count($recipe->xpath("ingredients/item")) ?>
		</div>
		<h2>
			<?php echo count($recipe->xpath("ingredients/item")) == 1? "item": "items" ?> remaining
		</h2>
	</div>
	<ul class="checklist">
	<?php
		// print ingredients for entire recipe, with common items summed
		$checkList = array();
		foreach ($recipe->ingredients->item as $recipeIngredient) {
			$ingredient = array();
			$ingredient["name"] = (string)$recipeIngredient;
			$ingredient["quantities"] = array();
			$ingredient["units"] = array();
			// extract ingredient quantities from dish ingredients and save to multidimensional array $checkList
			foreach ($recipe->dish as $dish) {
					foreach ($dish->ingredient as $dishIngredient) {
					if (preg_replace("/([0-9]+)\.[0-9]+/", "$1", $dishIngredient["id"]) == $recipeIngredient["id"]) {
						$ingredient["quantities"][] = isset($dishIngredient->quantity)?(float)$dishIngredient->quantity: null;
						$ingredient["units"][] = isset($dishIngredient->unit)? (string)$dishIngredient->unit: null;
					}
				}
			}
			$checkList[] = $ingredient;
		}
		// sort list alphabtically
		usort($checkList, "checkListCompare");

		// print out list items
		foreach ($checkList as $item) {
			echo "<li>";
			// determine whether one item (eg one tomato) is required in list
			$uniqueUnits = array_unique($item["units"]);
			$onlyOne = (count(array_unique($item["units"])) == 1 and reset($uniqueUnits) === null and ceil(array_sum($item["quantities"])) == 1);
			echo ucfirst(formatString($item["name"], !$onlyOne and array_unique($item["units"]) !== null));
			// print quantities and units, if required
			if (array_sum($item["quantities"]) > 0) {
				$unitList = array(); // array for storing strings of quantity and unit pairs, eg "400 g"
				// loop through all used units without dupliactes
				foreach (array_unique($item["units"]) as $uniqueUnit) {
					$quantityList = array(); // array for storing all quantities of a particular unit, to be summed
					// loop through all units and match with selected unique unit
					foreach ($item["units"] as $index => $unit) {
						if ($unit == $uniqueUnit)
							// append quantity to array
							$quantityList[] = $item["quantities"][$index];
					}
					// append formated quantity string to array
					$unitList[] = formatNumber(array_sum($quantityList)) . ($uniqueUnit !== null? "&nbsp;" . formatString($uniqueUnit, array_sum($quantityList) > 1): "");
				}
				// print all quantities and units for the current check list item
				if (!$onlyOne)
					echo  " <span class=\"info\">" . implode($unitList, " + ") . "</span>";
			}
			echo "</li>";
		}
	?>
	</ul>
</div>
