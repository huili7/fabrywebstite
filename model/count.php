<?Php
function read_last_line ($file_path){

	$line = '';
	$f = fopen($file_path, 'r');
	$cursor = -1;
	fseek($f, $cursor, SEEK_END);
	$char = fgetc($f);
 	//Trim trailing newline chars of the file
	while ($char === "\n" || $char === "\r") {
    	fseek($f, $cursor--, SEEK_END);
    	$char = fgetc($f);
	}
	// Read until the start of file or first newline char
	while ($char !== false && $char !== "\n" && $char !== "\r") {
    	//Prepend the new char    
    	$line = $char . $line;
   	 	fseek($f, $cursor--, SEEK_END);
    	$char = fgetc($f);
	}

	return $line;
}

       




$ip=$_SERVER['REMOTE_ADDR'];
$myrecord = read_last_line ("countlog.txt") ;
$myrecordarry=explode("!",$myrecord);
$count=$myrecordarry[1];
$count=$count + 1 ;
$record=$ip."!".$count;
echo "Your IP address: ".$ip." and you are ".$count." visitors.";
// opens countlog.txt to change new hit number
$datei = fopen("../document/countlog.txt","a");
fwrite($datei, $record."\n");
fclose($datei);
?>
