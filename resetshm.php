<?php
	require_once("src/shm.php");
	shm_remove($shm);
	echo "SHM reset";
?>