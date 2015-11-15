<div class="page" id="latest">
	<h2>Recent additions to the cookbook</h2>
	<?php
		for ($i = 0; $i < min(5, count($recipeIndex)); $i ++): ?>
			<div class="recent-box" style="background-image:url('<?= $recipeIndex[$i]["p"] ?>')">
				<a href="recipes/<?= str_replace(".json", "", $recipeIndex[$i]["f"]) ?>" class="title"><?= $recipeIndex[$i]["t"] ?></a>
			</div>
	<?php endfor ?>
</div>