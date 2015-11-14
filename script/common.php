<?php
	// find the base url of the site
	function findBase() {
		$domain = "cookbook.rocketchilli.com";
		$url = "http" . (isset($_SERVER["HTTPS"])? "s": "") . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$base = substr($url, 0, strpos($url, $domain) + strlen($domain));
		return $base . "/";
	}

	function recipeCompare($a, $b) {
		return strcmp($a->t, $b->t);
	}

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
	function parseDishIngredients($dish) {
		$strings = [];
		// read ingredient strings into $strings from method steps
		foreach ($dish["steps"] as $step) {
			preg_match_all("/\[#\d+|.*?\]/", (string)$step, $matches); // search for "[#1|quantity|prep|name]"
			$strings = array_merge($strings, $matches[0]); // append $strings with matches from regex
		}

		$ingredients = [];
		foreach ($strings as $string) {
			// extract quantity, unit and prep from dish ingredient list
			preg_match("/\[#(\d+)\|(?:([\d.]+) ?([^|]+)?\|)?(?:([^|]+)\|)?([\w\s]+)\]/", $string, $matches);
			$details = [];
			$details["id"] = strlen($matches[1]) > 0 ? $matches[1] : null;
			$details["text"] = strlen($matches[5]) > 0 ? $matches[5] : null;
			$details["quantity"] = strlen($matches[2]) > 0 ? $matches[2] : null;
			$details["unit"] = strlen($matches[3]) > 0 ? $matches[3] : null;
			$details["prep"] = strlen($matches[4]) > 0 ? $matches[4] : null;
			$ingredients[] = $details;
		}
		return $ingredients;
	}

	function getIngredientName($recipe, $id) {
		return $recipe["ingredients"][$id - 1];
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