<?php
require_once("db/auth/user.php");
$USER = new User();

$cid = $USER->get_post_value("cid");
$pid = $USER->get_post_value("pid");

$m_project = $USER->get_project_menu( $cid, $pid );
echo $m_project;
?>