<?php
//require_once 'autoload.php';
require_once 'fontkit.php';

$fk=new fontkit();
//$fk->matchFont('VTKS no name');
$quick="If you can't respect that, your perspective is whack. Maybe you'll love me when I fade to black.";
$im=$fk->gdrender('VTKS no name',$quick);
//$im=$fk->gdrender('Arial',$quick);
$im=$fk->gdrender('Palatino Linotype',$quick);
header('Content-Type: image/jpeg');
imagejpeg($im);
