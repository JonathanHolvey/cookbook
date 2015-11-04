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
		 <?php
		 	include("pages/ingredients.php");
		 	include("pages/method.php");
		 	include("pages/checklist.php");
		 ?>
		</div>
	</div>
</body>