<?php
/* This file is here to protect the current directory */
if (file_exists(dirname(dirname(__FILE__)) . '/Settings.php'))
{
	// Found it!
	require(dirname(dirname(__FILE__)) . '/Settings.php');
	header('Location: ' . $boardurl);
}
// Can't find it... just forget it.
else
	exit;
?>