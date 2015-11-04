<div class="page" id="method">
	<?php
		$number = 1;
		foreach ($recipe->dish as $dish) {
			echo "<h2>" . ucfirst($dish->name) . "</h2>";
			foreach ($dish->step as $step) {
				preg_match_all("/\[([\w\s]+)\|([0-9\.]+)\]/", $step, $matches);
				foreach ($matches[0] as $index => $match) {
					$details = ingredientFromId($matches[2][$index]);
					//print the ingredient name, formatted according to quantity
					$replace =  "<span class=\"ingredient\">" . $matches[1][$index];
					$replace .= "<span class=\"bubble\"><span class=\"name\">" . formatString($details["name"], !(!isset($details["unit"]) and $details["quantity"] <= 1)) . "</span><span class=\"info\">";
					// print the quantity and unit, if required. units are formatted according to quantity
					if (isset($details["quantity"]))
						$replace .= formatNumber($details["quantity"]) . (isset($details["unit"])? "&nbsp;" . formatString($details["unit"], $details["quantity"] > 1): "");
					else
						$replace .= "as required";
					// print preparation instructions, if reuqired
					$replace .= (isset($details["prep"])? " - " . $details["prep"]: "");
					$replace .= "</span></span></span>";

					$step = str_replace($match, $replace, $step);
				}
				echo "<p><span class=\"step\">Step " . $number . ":</span> " . $step . "</p>";
				$number ++;
			}
		}
	?>
</div>
