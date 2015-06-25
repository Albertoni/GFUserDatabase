<?php

ini_set('display_errors','off');

// Init autoloader

function fw_autoload($class) {
	if (is_file('app/objects/'.$class.'.class.php')) {
		include 'app/objects/'.$class.'.class.php';
	}
}

spl_autoload_register('fw_autoload');

// Route request

$parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
array_shift($parts); // remove 'gfusers'

if (count($parts)) {
	$controller = array_shift($parts);
	$action = @array_shift($parts);
} else {
	$controller = 'Homepage';
	$action = 'View';
}

// Init controller

if (!is_file('app/controllers/'.$controller.'.class.php')) {
	$controller = 'NotFound';
}

require 'app/controllers/'.$controller.'.class.php';
$controllername = $controller.'Controller';
$controller = new $controllername($action, $parts);
$controller->execute();

