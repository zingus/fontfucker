<?php
date_default_timezone_set('UTC');
class reg
{ 
  var $rootkeys=array(
    'HKEY_CLASSES_ROOT'   =>0x80000000,
    'HKEY_CURRENT_USER'   =>0x80000001,
    'HKEY_LOCAL_MACHINE'  =>0x80000002,
    'HKEY_USERS'          =>0x80000003,
    'HKEY_CURRENT_CONFIG' =>0x80000005,
    'HKEY_DYN_DATA'       =>0x80000006, 
    'HKCR'  =>0x80000000,
    'HKCU'  =>0x80000001,
    'HKLM'  =>0x80000002,
    'USERS' =>0x80000003,
    'HKCC'  =>0x80000005,
    'HKDD'  =>0x80000006, 
  );

  function reg()
  {
    $this->wmi=new COM('WinMgmts:StdRegProv');
  }

  function query($path)
  {
    $ret=new reg_query_results();
    
    $enum_keys   = new VARIANT(array());
    $enum_values = new VARIANT(array());
    
    $this->_parse_path($path,$key,$sub);
    
    $this->wmi->EnumKey($key,$sub,$enum_keys);
    $this->wmi->EnumValues($key,$sub,$enum_values);
    
    if(variant_get_type($enum_keys)==8204)
      foreach($enum_keys as $v) $ret->keys[]=$v;
    if(variant_get_type($enum_values)==8204)
      foreach($enum_values as $name) {
        $ret->values[$name]=$this->read(array($key,$sub),$name);;
      }
    return $ret;
  }

  function read($path,$value_name='')
  {
    if(is_array($path))
      list($key,$sub)=$path;
    else
      $this->_parse_path($path,$key,$sub);
    $value_data=new VARIANT('');
    $this->wmi->GetStringValue($key,$sub,$value_name,$value_data);
    $type=variant_get_type($value_data);
    switch($type)
    {
      case VT_EMPTY:
      case VT_NULL:
        return false;
      default:
        return (string)$value_data;
    }
  }

  function write($path,$value_name,$value_data,$type="REG_DWORD")
  {
    $this->_parse_path($path,$key,$sub);
    $this->wmi->SetStringValue($key,$sub,$value_name,$value_data);
  }
  
  function _parse_path($path,&$key,&$subkey)
  {
    @list($key,$subkey)=explode('\\',$path,2);
    $key=$this->rootkeys[$key];
    if(is_null($subkey)) $subkey='';
  }

  function export()
  {
    // ...
    // planned
  }
}

class reg_query_results
{
  var $values=array();
  var $keys=array();

  function __tostring()
  {
    $ret='';
    foreach($this->keys   as $name=>$data)
      $ret.=sprintf("k: %-20s -> %s\n",$name,$data);
    foreach($this->values as $name=>$data)
      $ret.=sprintf("v: %-20s -> %s\n",$name,$data);
    return $ret;
  }
}

//?$r=new reg();
//?var_export($r->query_keys('HKLM\Software'));
