<div class="page" id="ingredients">
	<?php
		// print ingredients for each dish, in the order listed in the method
		foreach ($recipe["dishes"] as $dish):
			echo "<h2>" . ucfirst($dish["name"]) . "</h2><ul>";
			// print ingredient list
			$ingredients = parseDishIngredients($dish);
			foreach ($ingredients as $ingredient):
				$name = ucfirst(formatString(getIngredientName($recipe, $ingredient["id"]), !(!$ingredient["unit"] and $ingredient["quantity"] <= 1)));
				$quantity = formatNumber($ingredient["quantity"]) . (isset($ingredient["unit"]) ? "&nbsp;" . formatString($ingredient["unit"], $ingredient["quantity"] > 1) : "");
			?>
				<li>
					<?= $name ?>
					<span class="info"><?= isset($ingredient["quantity"]) ? "- " . $quantity : "" ?><?= isset($ingredient["prep"]) ?  " - " . $ingredient["prep"] : "" ?></span>
				</li>
			<?php endforeach ?>
			</ul>
		<?php endforeach ?>
</div>