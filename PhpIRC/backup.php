<?php
$path = "../PhpIRC Backups/";
$filename = "PhpIRC-0.0.1-".date("mdY", time()).'-'.date("hia", time()).'.tar';
if(!file_exists("\"".$path.$filename.".gz\"")) {
	exec("tar cf \"".$path.$filename."\" * && gzip \"".$path.$filename."\"");
	echo "Archive ".$path.$filename." Created.\r\n";
} else {
	echo "Archive Already Exists!\r\n";
}
?>
