<?php

/**
 * Extend ErrorException so it can carry the reference to the symbol table.
 *
 * @see http://php.net/manual/en/function.set-error-handler.php
 */
class UniversalErrorCatcher_ErrorException extends ErrorException
{

  /**
   * @var array
   */
  protected $errcontext;

  /**
   * @param array $errcontext
   *   An array that points to the active symbol table at the point the
   *   error occurred.
   */
  public function setContext(array &$errcontext) {
    $this->errcontext = &$errcontext;
  }

  /**
   * @return array
   *   An array that points to the active symbol table at the point the
   *   error occurred.
   */
  public function getContext() {
    return $this->errcontext;
  }
}