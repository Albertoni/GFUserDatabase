<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsModifier
 */


/**
 * Smarty indent modifier plugin
 *
 * Type:     modifier<br>
 * Name:     indent<br>
 * Purpose:  indent lines of text
 * @link http://smarty.php.net/manual/en/language.modifier.indent.php
 *          indent (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @return string
 */
function smarty_modifier_relative_time($timestamp)
{
	$secs = time() - $timestamp;

	if ($secs < 10) {
		return 'moments ago';
	}

	if ($secs < 60) {
		return 'less than a minute ago';
	}

	$mins = floor($secs / 60);

	if ($mins == 1) {
		return 'a minute ago';
	}

	if ($mins < 60) {
		return $mins.' minutes ago';
	}

	$hours = floor($mins / 60);

	if ($hours == 1) {
		return 'an hour ago';
	}

	if ($hours < 48) {
		return $hours.' hours ago';
	}

	$days = floor($hours / 24);

	return $days.' days ago';
}

?>
