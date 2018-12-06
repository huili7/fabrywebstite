<?php

function cmd_exec($cmd, &$stdout, &$stderr)
{
    $outfile = tempnam(".", "cmd");
    $errfile = tempnam(".", "cmd");
    $descriptorspec = array(
        0 => array("pipe", "r"),
        1 => array("file", $outfile, "w"),
        2 => array("file", $errfile, "w")
    );
    $proc = proc_open($cmd, $descriptorspec, $pipes);

    if (!is_resource($proc)) return 255;

    fclose($pipes[0]);    //Don't really want to give any input

    $exit = proc_close($proc);
    $stdout = file($outfile);
    $stderr = file($errfile);

    unlink($outfile);
    unlink($errfile);
    return $exit;
}



$myfile = fopen("../view/distribute.php", "w") or die("Unable to open file!");
$txt = $_POST['name'];
fwrite($myfile, $txt);
echo "success fule";
fclose($myfile);
    $cmdpipeline='./sendEmail.sh';
    cmd_exec($cmdpipeline,$returnvalue,$error7);
    cmd_exec("whoami",$returnvalue,$error7);
    $return['error'] = true;
    $return['msg'] = "Successfully  Sending the email";
    echo json_encode($return);
?>
