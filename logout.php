<?php 
	session_start();
	$_SESSION = array();

	header('Location: http://'.$_SERVER['HTTP_HOST'].'');
	exit();
?>