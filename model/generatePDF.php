<?php
include('../mpdf60/mpdf.php');

$html= file_get_contents('../document/pdf.txt', FILE_USE_INCLUDE_PATH);
$mpdf=new mPDF();
$mpdf->WriteHTML($html);
$mpdf->Output('test.pdf','D');   exit;
 ?>
