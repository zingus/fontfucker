<?php
//require_once 'autoload.php';
require_once 'fontkit.php';

$text="The quick brown fox jumps over the lazy dog";
if(@$_GET['text']) $text=$_GET['text'];

echo "<form>";
echo "<p style='position:fixed;right:20px;top:0;width:100px;text-align:center'><input type='submit'></p>";
echo "<table>";
$fk=new fontkit();
foreach($fk->listfonts() as $family) {
  echo "<tr style='font-family:$family; font-size:24pt;white-space: nowrap' title='$family'>
  <td><input type='checkbox' name='$family' id='$family'/>";
  $l1="<label for='$family'>";
  $l2="</label>";
  $uc1=ucwords(strtolower($text));
  $uc2=ucfirst(strtolower($text));
  echo "<td>$l1$uc1";
  if($uc1!=$uc2) echo " &mdash; $uc2";
  echo " &mdash; ".strtolower($text)." &mdash; ".strtoupper($text).$l2;
}
echo "</table></form>";
