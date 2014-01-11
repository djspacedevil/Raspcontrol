<?php

namespace lib;

class Rbpi {
  
  public static function distribution() {
    $distroTypeRaw = exec("cat /etc/*-release | grep PRETTY_NAME=", $out);
    $distroTypeRawEnd = str_ireplace('PRETTY_NAME="', '', $distroTypeRaw);
    $distroTypeRawEnd = str_ireplace('"', '', $distroTypeRawEnd);

    return $distroTypeRawEnd;
  }

  public static function kernel() {
    return exec("uname -mrs");
  }

  public static function firmware() {
    return exec("uname -v");
  }

  public static function hostname($full = false) {
    return $full ? exec("hostname -f") : gethostname();
  }

  public static function internalIp() {
    return $_SERVER['SERVER_ADDR'];
  }

  public static function externalIp() {
    if (!function_exists('file_get_contents'))
      return 'Unavailable';
    $ip_json = file_get_contents('http://pv.sohu.com/cityjson?ie=utf-8');
    if ($ip_json==FALSE)
      return 'Unavailable';
    $ip_json = trim($ip_json);
    $ip_json = substr($ip_json, 19);
    $ip_json = substr($ip_json, 0, -1);
    $ip_arr = json_decode($ip_json,true);
    return $ip_arr['cip'];
  }

  public static function webServer() {
    return$_SERVER['SERVER_SOFTWARE'];
  }

}

?>
