<!DOCTYPE html>
<?php
	require_once("script/common.php");

	$title = $_GET["title"];
	$recipe = json_decode(file_get_contents("recipes/" . $title . ".json"), true);

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?= $recipe["title"] ?></title>
	<base href="<?= findBase() ?>"/>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<?php include("resources.php") ?>
	<link rel="stylesheet" type="text/css" href="./styles/recipe.css"/>
	<link rel="prefetch" content="images/tick.svg"/>
</head>
<body>
	<div class="header">
		<div class="container">
			<h1>
				<a href="."><img class="logo" src="images/logo.svg" alt=""/></a>
				<?= $recipe["title"] ?>
			</h1>
			<hr/>
			<h3>
				<?= $recipe["description"] ?>
				<span class="date"><?= isset($recipe["date"])? date("j M Y", strtotime($recipe["date"])): "" ?></span>
			</h3>
		</div>
	</div>
	<div class="recipe-info container">
		<div class="block">
			<div class="title">Difficulty</div>
			<div class="value"><?= $recipe["difficulty"] ?></div>
		</div>
		<div class="block">
			<div class="title">Serves</div>
			<div class="value"><?= intToString($recipe["serves"]) ?></div>
		</div>
		<div class="block">
			<div class="title">Prep time</div>
			<div class="value"><?= convertTime($recipe["preparation"]) ?></div>
		</div>
		<div class="block">
			<div class="title">Cook time</div>
			<div class="value"><?= convertTime($recipe["cooking"]) ?></div>
		</div>
	</div>
	<div class="recipe-photo container">
		<img src="<?= $recipe["image"] ?>" alt=""/>
		<div class="credits">
			<div class="name">Original recipe by <?php echo $recipe["author"] ?></div>
			<div class="link">
				Modified from
				<?php
					$sources = array();
					foreach ($recipe["sources"] as $url) {
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