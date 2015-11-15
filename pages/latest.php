<div class="page" id="latest">
	<h2>Recent additions to the cookbook</h2>
	<?php
		for ($i = 0; $i < min(5, count($recipeIndex)); $i ++): ?>
			<a href="recipes/<?= str_replace(".json", "", $recipeIndex[$i]["f"]) ?>" class="recent-box" style="background-image:url('<?= $recipeIndex[$i]["p"] ?>')">
				<div class="title"><?= $recipeIndex[$i]["t"] ?></div>
			</a>
	<?php endfor ?>
</div>