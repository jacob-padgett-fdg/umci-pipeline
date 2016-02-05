<?php
require_once("settings.cfg");
require_once("global-auth.inc");
require_once("querylib.inc");

// check do any daily log entries already exist
$sql = "SELECT * FROM dialylog_entries WHERE dailylog_id = '".$dailylog_id."'";
$result = mysql_query( $sql );
$entry_count = mysql_num_rows();

// if not, delete the dailylog record
if ($entry_count == 0)
{
    $sql = "DELETE FROM dailylog WHERE dialylog_id = '".$dailylog_id."'";
    $result = mysql_query( $sql );
}

// go back to dailylog/index.php

header("location: /global/front_desk/dailylog/");

?>