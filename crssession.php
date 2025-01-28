<?php

if (!session_id())
{
	session_start();
}

if(isset($_SESSION['u_sno']) != session_id())
{
	header('Location: login.php');
}


?>