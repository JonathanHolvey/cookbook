<?php
	require_once("script/common.php");
	$recipeIndex = json_decode(file_get_contents("recipe-index.json"), true);

	function recipeDateCompare($a, $b) {
		return -1 * strcmp(strtotime($a["d"]), strtotime($b["d"]));
	}

	usort($recipeIndex, "recipeDateCompare");
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Rocket Chilli Cookbook</title>
	<base href="<?= findBase() ?>"/>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<?php include("resources.php") ?>
	<link rel="stylesheet" type="text/css" href="styles/index.css"/>
	<script type="text/javascript" src="script/jquery.highlight.js"></script>
	<script type="text/javascript" src="script/search.js"></script>
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
			<div class="page-tab" data-page="list">A-Z</div>
		</div>
		<div class="page-holder">
		 <?php
		 	include("pages/latest.php");
		 	include("pages/genres.php");
		 	include("pages/search.php");
		 	include("pages/list.php");
		 ?>
		</div>
	</div>
</body>