<?php
if (!function_exists('curl_init')) { 
  die('CURL не установлен!');
}
function stringvar($string) {
  if (!get_magic_quotes_gpc()) $string=addslashes($string);
  $string=htmlspecialchars(trim(strip_tags($string)), ENT_QUOTES);
  return $string;
}
function testint($int,$count=1) {
  if (!is_numeric($int)) {
    return false;
  } else {
    if (preg_match("/^[0-9]{".$count."}$/",$int)) {
      return true;
    } else {
      return false;
    }
  }
}
function gencode($length=40) {
  $chars="qwertyuiopasdfghjklzxcvbnm1234567890";
  $code="";
  $clen=strlen($chars)-1;
  while (strlen($code)<$length) {
    $code.=$chars[mt_rand(0,$clen)];
  }
  return $code;
}
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
  throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
function cURL($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);    
  curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);    
  $result = curl_exec($ch);
  curl_close($ch);
  if ($result){
    return json_decode($result,true);
  }else{
    return '';
  }
}