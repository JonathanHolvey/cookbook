<div class="page" id="method">
	<?php
		$number = 1;
		foreach ($recipe["dishes"] as $dish) {
			echo "<h2>" . ucfirst($dish["name"]) . "</h2>";
			foreach ($dish["steps"] as $step) {
				$ingredients = parseDishIngredients($dish);
				// preg_match_all("/\[([\w\s]+)\|([0-9\.]+)\]/", $step, $matches);
				foreach ($ingredients as $ingredient) {
					//print the ingredient name, formatted according to quantity
					$replace =  "<span class=\"ingredient\">" . $ingredient["text"];
					$replace .= "<span class=\"bubble\"><span class=\"name\">" . formatString(getIngredientName($recipe, $ingredient["id"]), !(!isset($ingredient["unit"]) and $ingredient["quantity"] <= 1)) . "</span><span class=\"info\">";
					// print the quantity and unit, if required. units are formatted according to quantity
					if (isset($ingredient["quantity"]))
						$replace .= formatNumber($ingredient["quantity"]) . (isset($ingredient["unit"])? "&nbsp;" . formatString($ingredient["unit"], $ingredient["quantity"] > 1): "");
					else
						$replace .= "as required";
					// print preparation instructions, if reuqired
					$replace .= (isset($ingredient["prep"])? " - " . $ingredient["prep"]: "");
					$replace .= "</span></span></span>";

					$step = str_replace($ingredient["string"], $replace, $step);
				}
				echo "<p><span class=\"step\">Step " . $number . ":</span> " . $step . "</p>";
				$number ++;
			}
		}
	?>
</div>
