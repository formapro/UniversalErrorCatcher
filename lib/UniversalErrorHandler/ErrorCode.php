<?php

/**
 * 
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
abstract class UniversalErrorHandler_ErrorCode
{ 
  const 
    E_ERROR = E_ERROR,
    E_RECOVERABLE_ERROR = E_RECOVERABLE_ERROR,
    E_WARNING = E_WARNING,
    E_PARSE = E_PARSE,
    E_NOTICE = E_NOTICE,
    E_STRICT = E_STRICT, 
    E_CORE_ERROR = E_CORE_ERROR,
    E_CORE_WARNING = E_CORE_WARNING,
    E_COMPILE_ERROR = E_COMPILE_ERROR,
    E_COMPILE_WARNING = E_COMPILE_WARNING,
    E_USER_ERROR = E_USER_ERROR,
    E_USER_WARNING = E_USER_WARNING,
    E_USER_NOTICE = E_USER_NOTICE,
    E_ALL = E_ALL,
    E_UNKNOWN = 'E_UNKNOWN';
  
  /**
   * 
   * @return array
   */
  public static function getFatals()
  {
    return array(
      self::E_ERROR, 
      self::E_PARSE, 
      self::E_CORE_ERROR, 
      self::E_COMPILE_ERROR, 
      self::E_USER_ERROR);
  }
  
  public static function getAll()
  {
    $c = new ReflectionClass(__CLASS__);
    
    return $c->getConstants();
  }
  
  /**
   *
   * @param int $code
   * 
   * @return string
   */
  public static function getName($code)
  {
    $key = array_search($code, self::getAll());
    
    return $key ? $key : self::E_UNKNOWN;
  }
  
  /**
   * 
   * @param string $name
   * 
   * @return int|string
   */
  public static function getCode($name)
  {
    $errors = self::getAll();
    
    return array_key_exists($name, $errors) ? $errors[$name] : self::E_UNKNOWN;
  }
}