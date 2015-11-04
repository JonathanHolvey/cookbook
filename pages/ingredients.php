<div class="page" id="ingredients">
	<?php
		// print ingredients for each dish, in the order listed in the method
		foreach ($recipe->dish as $dish) {
			echo "<h2>" . ucfirst($dish->name) . "</h2><ul>";
			$idList = array();
			// read ingredient ids into $idList from method steps
			foreach ($dish->step as $step) {
				preg_match_all("/\[[\w\s]+\|([0-9\.]+)\]/", (string)$step, $matches); // search for "[name|1.2]"
				$idList = array_merge($idList, $matches[1]); // append $idList with matches from regex
				$idList = array_unique($idList); // remove duplicate entries
			}
			// print ingredient list
			foreach ($idList as $id) {
				echo "<li>";

				$details = ingredientFromId($id);

				//print the ingredient name, formatted according to quantity
				echo ucfirst(formatString($details["name"], !(!isset($details["unit"]) and $details["quantity"] <= 1))) . " <span class=\"info\">";
				// print the quantity and unit, if required. units are formatted according to quantity
				if (isset($details["quantity"]))
					echo formatNumber($details["quantity"]) . (isset($details["unit"])? "&nbsp;" . formatString($details["unit"], $details["quantity"] > 1): "");
				// print preparation instructions, if reuqired
				echo (isset($details["prep"])? " - " . $details["prep"]: "");
				echo "</span></li>";
			}
			echo "</ul>";
		}
	?>
</div>
