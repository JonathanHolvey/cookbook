<div class="page" id="ingredients">
	<?php
		// print ingredients for each dish, in the order listed in the method
		foreach ($recipe["method"] as $dish) {
			echo "<h2>" . ucfirst($dish["dish"]) . "</h2><ul>";
			$ingredients = [];
			// read ingredient ids into $ingredients from method steps
			foreach ($dish["steps"] as $step) {
				preg_match_all("/\[#\d+|.*?\]/", (string)$step, $matches); // search for "[#1|quantity|prep|name]"
				$ingredients = array_merge($ingredients, $matches[0]); // append $ingredients with matches from regex
			}
			// print ingredient list
			foreach ($ingredients as $string) {
				echo "<li>";

				$details = parseIngredient($string);

				//print the ingredient name, formatted according to quantity
				echo ucfirst(formatString(getIngredientName($recipe, $details["id"]), !(!$details["unit"] and $details["quantity"] <= 1))) . " <span class=\"info\">";
				// print the quantity and unit, if required. units are formatted according to quantity
				if ($details["quantity"])
					echo formatNumber($details["quantity"]) . ($details["unit"] ? "&nbsp;" . formatString($details["unit"], $details["quantity"] > 1): "");
				// print preparation instructions, if reuqired
				echo $details["prep"] ? " - " . $details["prep"] : "";
				echo "</span></li>";
			}
			echo "</ul>";
		}
	?>
</div>
