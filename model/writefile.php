<?php
$myfile = fopen("../document/pdf.txt", "w") or die("Unable to open file!");
$txt = $_POST['name'];
fwrite($myfile, $txt);
echo "success fule";
fclose($myfile);
?>
