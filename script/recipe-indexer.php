<?php
	/* key for abbreviated node names
	t = title
	a = description (about)
	d = date
	f = file
	p = image (picture)
	i = ingredient
	g = genre
	r = recipe */

	$recipeIndex = new simpleXMLElement("<index/>");

	foreach(scandir("../recipes") as $fileName) {
		if (preg_match("/(.+)\.xml$/", $fileName, $matches) == 1 and $fileName != "recipe-index.xml") {
			$file = simplexml_load_file("../recipes/" . $fileName);
			$index = $recipeIndex->addChild("r");
			$index->addChild("t", $file->title);
			$index->addChild("d", $file->date);
			$index->addChild("a", $file->description);
			$index->addChild("p", $file->image);
			$index->addChild("f", $fileName);
			foreach ($file->ingredients->children() as $ingredient)
				$index->addChild("i", $ingredient);
			foreach ($file->genre as $genre)
				$index->addChild("g", $genre);
		}
	}

	$recipeIndex->asXML("../recipe-index.xml");
?>