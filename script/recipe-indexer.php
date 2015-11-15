<?php
	/* key for abbreviated object names
	t = title
	a = description (about)
	d = date
	f = file
	p = image (picture)
	i = ingredient
	g = genre
	r = recipe */

	$recipeIndex = [];

	foreach(scandir("../recipes") as $fileName) {
		if (preg_match("/(.+)\.json$/", $fileName, $matches) == 1) {
			$file = json_decode(file_get_contents("../recipes/" . $fileName), true);
			$index = [];
			$index["t"] = $file["title"];
			$index["d"] = $file["date"];
			$index["a"] = $file["description"];
			$index["p"] = $file["image"];
			$index["f"] = $fileName;
			$index["i"] = [];
			foreach ($file["ingredients"] as $ingredient)
				$index["i"][] = $ingredient;
			$index["g"] = [];
			foreach ($file["genres"] as $genre)
				$index["g"][] = $genre;
			$recipeIndex[] = $index;
		}
	}

	$output = json_encode($recipeIndex);
	file_put_contents("../recipe-index.json", $output);
?>