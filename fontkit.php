<?php
//run with php -dextension=php_com_dotnet.dll
require_once 'reg.php';

class fontkit
{
  function listfonts()
  {
    $reg=new reg();
    $res=$reg->query('HKLM\SOFTWARE\Microsoft\Windows NT\CurrentVersion\Fonts');
    $ret=array();
    foreach($res->values as $k=>$v) {
      $k=preg_replace('/([\s-]*(Bold|Italic|Wide|Xtnd)[\s-]*)*[\s-]*\(TrueType\)/i','',$k,-1,$count); 
      if($count) {
        $ret[$k]=1;
      }
    }
    ksort($ret);
    return array_keys($ret);
  }

  function resolveFont($name)
  {
    if(@!$this->fontNameCache) $this->buildCache();
    $reName=preg_quote($name);
    preg_match_all("#\\b$reName\\b[^\\n\\0]*\\0(.*)#mi",$this->fontNameCache,$M);
    
    $ret=array();
    foreach($M[1] as $bn) {
      $fn='C:\Windows\Fonts\\'.$bn;
      if(file_exists($fn)) $ret[]=$fn;
    }
    return $ret;
  }

  function buildCache()
  {
    $reg=new reg();
    $res=$reg->query('HKLM\SOFTWARE\Microsoft\Windows NT\CurrentVersion\Fonts');
    ksort($res->values);
    $cache='';
    foreach($res->values as $k=>$v)
      $cache.="$k\0$v\n";
    $cache=preg_replace('# *\(TrueType\) *#','',$cache);
    $this->fontNameCache=$cache;
  }

  function gdrender($font,$text)
  {
    $sz=128;
    $resolveFont=$this->resolveFont($font);
    list($blX,$blY,$brX,$brY,$trX,$trY,$tlX,$tlY)=
      imageftbbox($sz,0,$resolveFont[0],$text); 

    $x=+$tlX;
    $y=-$trY;

    $w=$trX-$tlX+4;
    $h=$blY-$tlY;
   
/*
    file_put_contents('poop.out',
    "$blX,$blY,$brX,$brY,$trX,$trY,$tlX,$tlY
$x $y
$w $h");
*/

    $im    = imagecreatetruecolor($w,$h);
    $black = imagecolorallocate($im, 0, 0, 0);
    $white = imagecolorallocate($im, 255, 255, 255);
    imagefilledrectangle($im, 0, 0, $w, $h, $white);
    imagefttext($im,$sz,0,$x,$y,$black,$resolveFont[0],$text);
    return $im;
  }
}
