<?php
	// find the base url of the site
	function findBase() {
		$domain = "cookbook.rocketchilli.com";
		$url = "http" . (isset($_SERVER["HTTPS"])? "s": "") . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$base = substr($url, 0, strpos($url, $domain) + strlen($domain));
		return $base . "/";
	}
?>