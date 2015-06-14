<?php
	// return this page's url with $levels directories stripped from the end
	function goUp($levels) {
		$url = "http" . (isset($_SERVER["HTTPS"])? "s": "") . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$parts = explode("/", $url);
		$length = count($parts);
		$parts = array_slice($parts, 0, $length - $levels);
		return implode("/", $parts) . "/";
	}
?>