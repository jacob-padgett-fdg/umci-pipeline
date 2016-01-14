<?
require_once("settings.cfg");
require_once("global-auth.inc");

$section_info=select_a_section();

if (isset($mode)&&($mode!="")) {
	if (is_readable("$mode.phtml")) { 
		include("$mode.phtml");  } else { include ("badmode.phtml"); }
	} else {
	include ("main.phtml");
	}
?>
