<?php 

/** 
 * @author Kotlyar Maksim kotlyar.maksim@gmail.com
 */
class UniversalErrorHandler_Handler
{
   /**
    * 
    * @var string
    */
    protected $memoryReserv = '';
    
    /**
     *
     * @var mixed
     */
    protected $callback;
    
    /**
     *
     * @param mixed $callbale a valid callback
     * 
     * @throws InvalidArgumentException if invalid callback provided
     */
    public function __construct($callback) 
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Invalid callback provided.');
        }
        
        $this->callback = $callback;
    }
  
    /**
    * 
    * @return void
    */
    public function start()
    {
        $this->memoryReserv = str_repeat('x', 1024 * 500);

        // Register error handler it will process the most part of erros (but not all)
        set_error_handler(array($this, 'handleError'));
        // Register shutdown handler it will process the rest part of errors
        register_shutdown_function(array($this, 'handleFatalError'));

        set_exception_handler(array($this, 'handleException'));
    }

    /**
    * 
    * @param Exception $e
    * 
    * @return void
    */
    public function handleException(Exception $e)
    {
        call_user_func_array($this->callback, array($e));
    }

    /**
    * 
    * @param string $errno
    * @param string $errstr
    * @param string $errfile
    * @param string $errline
    * 
    * @return ErrorException
    */
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        $this->handleException(new ErrorException($errstr, 0, $errno, $errfile, $errline));

        return false;
    }

    /**
    * 
    * @return void
    */
    public function handleFatalError()
    {
        $error = error_get_last();

        $skipHandling = 
          !$error || 
          !isset($error['type']) || 
          !in_array($error['type'], UniversalErrorHandler_ErrorCode::getFatals());
        if ($skipHandling) return;

        $this->freeMemory();

        @$this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
    }

    /**
    * 
    * @return void
    */
    protected function freeMemory()
    {
        unset($this->memoryReserv);
    }
}