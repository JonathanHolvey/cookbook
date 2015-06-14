<?php
	require_once("script/common.php");
	$recipeIndex = (array)simplexml_load_file("recipe-index.xml");
	$recipeIndex = $recipeIndex["r"];

	function recipeDateCompare($a, $b) {
		return -1 * strcmp(strtotime($a->d), strtotime($b->d));
	}

	usort($recipeIndex, "recipeDateCompare");
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Rocket Chilli Cookbook</title>
	<base href="<?php echo findBase() ?>"/>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<?php include("resources.php") ?>
	<link rel="stylesheet" type="text/css" href="styles/index.css"/>
	<script type="text/javascript" src="script/jquery.highlight.js"></script>
	<script type="text/javascript" src="script/search.js"></script>
	<script type="text/javascript">pageRoot = location.href.replace(/(latest|genres.*|index|search.*)/, "").replace(/\/$/, "")</script>
</head>
<body>
	<div class="header">
		<div class="container">
			<h1>
				<img class="logo" src="images/logo.svg" alt=""/>
				<img class="text-mark" src="images/rocket-chilli.svg" alt="The Rocket Chilli"/>
				Cookbook
			</h1>
			<hr/>
			<h3>Recipes from around the internet</h3>
		</div>
	</div>
	<div class="container">
		<div class="tab-holder">
			<div class="page-tab" data-page="latest">Latest</div>
			<div class="page-tab" data-page="genres">Genres</div>
			<div class="page-tab" data-page="search">Search</div>
			<div class="page-tab" data-page="index">A-Z</div>
		</div>
		<div class="page-holder">
			<div class="page" id="latest">
				<h2>Recent additions to the cookbook</h2>
				<?php
					for ($i = 0; $i < 5; $i ++)
						echo "<div class=\"recent-box\" style=\"background-image:url('" . $recipeIndex[$i]->p . "')\">
							<a href=\"recipes/" . str_replace(".xml", "", $recipeIndex[$i]->f) . "\" class=\"title\">" . $recipeIndex[$i]->t . "</a>
						</div>";
				?>
			</div>
			<div class="page" id="genres">This isn't ready yet</div>
			<div class="page" id="search">
				<div id="search-box">
					<div class="placeholder">Search for a recipe...</div>
					<input type="text"/>
					<div class="search-icon"></div>
				</div>
				<p id="search-no-match" class="error">No matching recipes</p>
				<p id="search-hint">Search for a recipe by its name or description, ingredients, region or style.</p>
				<div id="search-results"></div>
			</div>
			<div class="page" id="index">This isn't ready yet</div>
		</div>
	</div>
</body>