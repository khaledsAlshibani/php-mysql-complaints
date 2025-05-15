<?php

class Req
{
  public static function retrievePostValue($key, $trimValue = true, $default = '')
  {
    if (empty($key)) return $default;
    
    $sanitizedKey = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
    $value = isset($_POST[$sanitizedKey]) ? $_POST[$sanitizedKey] : $default;
    return $trimValue ?
      htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8') :
      htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  }

  public static function retrieveGetValue($key = '', $default = '')
  {
    if (empty($key)) return $default;
    
    $sanitizedKey = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
    return isset($_GET[$sanitizedKey]) ?
      htmlspecialchars($_GET[$sanitizedKey], ENT_QUOTES, 'UTF-8') :
      $default;
  }
}
