<div class="page" id="latest">
	<h2>Recent additions to the cookbook</h2>
	<?php
		for ($i = 0; $i < 5; $i ++)
			echo "<div class=\"recent-box\" style=\"background-image:url('" . $recipeIndex[$i]->p . "')\">
				<a href=\"recipes/" . str_replace(".xml", "", $recipeIndex[$i]->f) . "\" class=\"title\">" . $recipeIndex[$i]->t . "</a>
			</div>";
	?>
</div>
