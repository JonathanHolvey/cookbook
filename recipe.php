<?php
	require_once("script/common.php");

	$title = $_GET["title"];
	$recipe = simplexml_load_file("recipes/" . $title . ".xml");

	// convert common fractions to html entities
	function formatNumber($number) {
		switch ($number) {
			case 0.5: return "&frac12;";
			case 0.25: return "&frac14;";
			case 0.75: return "&frac34;";
			case 0.33: return "&#8531;";
			case 0.66: return "&#8532;";
		}
		return $number;
	}

	// converts strings such as tomato(es) and leaf/leaves to tomato and leaf or tomatoes and leaves
	function formatString($string, $many) {
		if ($many)
			return preg_replace("/(\w+)\((\w*s)\)|(\w+)\/(\w+s)/", "$1$2$4", $string);
		else
			return preg_replace("/(\w+)\((\w*s)\)|(\w+)\/(\w+s)/", "$1$3", $string);
	}

	// finds ingredient details based on an id
	function ingredientFromId($id) {
		global $recipe;
		// extract name from recipe ingredient list
		$recipeXpath = $recipe->xpath("ingredients/item[@id=\"" . floor($id) . "\"]");
		$name = reset($recipeXpath);

		if (strpos($id, ".") !== false) {
			// extract quantity, unit and prep from dish ingredient list
			$dishXpath = $recipe->xpath("dish/ingredient[@id=\"" . $id . "\"]");

			$details = 	array();
			$details["name"] = (string)$name;
			$details["quantity"] = isset(reset($dishXpath)->quantity)? reset($dishXpath)->quantity: null;
			$details["unit"] = isset(reset($dishXpath)->unit)? reset($dishXpath)->unit: null;
			$details["prep"] = isset(reset($dishXpath)->prep)? reset($dishXpath)->prep: null;
			// returns array of all details if full id (10.2) is used
			return $details;
		}
		else // returns just the ingredient name if short id (10) is used
			return $name;
	}

	// converts numbers to text representation
	function intToString($number) {
		$strings = array("zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten");
		if ((int)$number >= 0 and (int)$number <= 10)
			return $strings[(int)$number];
		else
			return $number;
	}

	// converts times of less than an hour to just minutes, eg 10 min
	function convertTime($time) {
		if (strpos($time, ":") !== false) {
			preg_match("/([0-9]+):([0-9]{2})/", $time, $matches);
			$hours = (int)$matches[1];
			$minutes = (int)$matches[2];
			if ($hours <= 0)
				return $minutes . "&nbsp;min";
		}
		return $time;
	}

	// function for custom sorting of check list array
	function checkListCompare($a, $b) {
		return strcasecmp($a["name"], $b["name"]);
	}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo $recipe->title ?></title>
	<base href="<?php echo findBase() ?>"/>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<?php include("resources.php") ?>
	<link rel="stylesheet" type="text/css" href="./styles/recipe.css"/>
	<link rel="prefetch" content="images/tick.svg"/>
	<script type="text/javascript">pageRoot = location.href.replace(/(ingredients|method|checklist)/, "").replace(/\/$/, "");</script>
</head>
<body>
	<div class="header">
		<div class="container">
			<h1>
				<a href="."><img class="logo" src="images/logo.svg" alt=""/></a>
				<?php echo $recipe->title ?>
			</h1>
			<hr/>
			<h3>
				<?php echo $recipe->description ?>
				<span class="date"><?php echo isset($recipe->date)? date("j M Y", strtotime($recipe->date)): "" ?></span>
			</h3>
		</div>
	</div>
	<div class="recipe-info container">
		<div class="block">
			<div class="title">Difficulty</div>
			<div class="value"><?php echo $recipe->difficulty ?></div>
		</div>
		<div class="block">
			<div class="title">Serves</div>
			<div class="value"><?php echo intToString($recipe->serves) ?></div>
		</div>
		<div class="block">
			<div class="title">Prep time</div>
			<div class="value"><?php echo convertTime($recipe->preparation) ?></div>
		</div>
		<div class="block">
			<div class="title">Cook time</div>
			<div class="value"><?php echo convertTime($recipe->cooking) ?></div>
		</div>
	</div>
	<div class="recipe-photo container">
		<img src="<?php echo $recipe->image ?>" alt=""/>
		<div class="credits">
			<div class="name">Original recipe by <?php echo $recipe->author ?></div>
			<div class="link">
				Modified from
				<?php
					$sources = array();
					foreach ($recipe->source as $url) {
						$text = preg_replace("/https?:\/\/([\w\.]+)\/.*/", "$1", $url);
						$sources[] = "<a href=\"" . $url . "\">" . $text . "</a>";
					}
					echo implode(", ", $sources);
				?>
			</div>
		</div>
	</div>
	<div class="recipe-instructions container">
		<div class="tab-holder">
			<div class="page-tab" data-page="ingredients">Ingredients</div>
			<div class="page-tab" data-page="method">Method</div>
			<div class="page-tab" data-page="checklist">Checklist</div>
		</div>
		<div class="page-holder">
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
		</div>
	</div>
</body>