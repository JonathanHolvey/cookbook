<div class="page" id="list">
	<?php
		// create array of empty arrays with letters as keys
		$letters = ["#", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
		$letters = array_flip($letters);
		foreach ($letters as $key => $value)
			$letters[$key] = [];
		// sort recipes into letters array based on first character of the title
		foreach ($recipeIndex as $recipe) {
			$letter = strtolower(substr($recipe["t"], 0, 1));
			if (array_key_exists($letter, $letters))
				array_push($letters[$letter], $recipe);
			else
				array_push($letters["#"], $recipe);
		}
	?>
	<div class="letter-picker">
		<?php foreach($letters as $letter => $recipes): ?>
		<a class="letter<?= count($recipes) === 0 ? ' empty' : '' ?>" href="#<?= urlencode($letter) ?>"><?= $letter ?></a>
		<?php endforeach; ?>
	</div>

	<?php foreach($letters as $letter => $recipes):
		usort($recipes, "recipeCompare"); ?>
		<div id="<?= urlencode($letter) ?>" class="list-letter<?= count($recipes) === 0 ? ' empty' : '' ?>">
			<h1><?= $letter ?></h1>
			<?php foreach ($recipes as $recipe): ?>
				<a href="recipes/<?= str_replace('.json', '', $recipe["f"]) ?>" class="result">
					<h2><?= $recipe["t"] ?></h2>
					<p><?= $recipe["a"] ?></p>
					<p class="details"><span class="title">Genres:</span> <?= str_replace("/", "&nbsp;&rsaquo; ", implode(", ", (array)$recipe["g"])) ?></p>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>
</div>
