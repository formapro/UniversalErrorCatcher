<?php

/**
 *
 * @author Kotlyar Maksim kotlyar.maksim@gmail.com
 */
class UniversalErrorCatcher_Tests_CatcherTest extends PHPUnit_Framework_TestCase
{
    protected $errorData = array(
        'errstr' => 'foo',
        'errno' => 10,
        'errfile' => 'bar.php',
        'errline' => 100
    );

    protected $invalidFatalData = array(
        'type' => 'foo',
        'message' => 10,
        'file' => 'bar.php',
        'line' => 100
    );

    protected $fatalData = array(
        'type' => E_ERROR,
        'message' => 10,
        'file' => 'bar.php',
        'line' => 100
    );

    /**
     *
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid callback provided.
     */
    public function shouldThrowIfInvalidCallbackProvided()
    {
        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback('foo');
    }

    /**
     *
     * @test
     */
    public function shouldAllowToRegisterCallback()
    {
        $callback = array(new ____Callback, 'handle');

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback($callback);

        $this->assertAttributeContains($callback, 'callbacks', $catcher);
    }

    /**
     *
     * @test
     *
     * @depends shouldAllowToRegisterCallback
     */
    public function shouldAllowToRegisterMultipleCallbacks()
    {
        $callbackOne = array(new ____Callback, 'handle');
        $callbackTwo = array(new ____Callback, 'handle');

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback($callbackOne);
        $catcher->registerCallback($callbackTwo);

        $this->assertAttributeCount(2, 'callbacks', $catcher);
        $this->assertAttributeContains($callbackOne, 'callbacks', $catcher);
        $this->assertAttributeContains($callbackTwo, 'callbacks', $catcher);
    }

    /**
     *
     * @test
     *
     * @depends shouldAllowToRegisterCallback
     * @depends shouldAllowToRegisterMultipleCallbacks
     */
    public function shouldAllowUnregisterCallback()
    {
        $callbackOne = array(new ____Callback, 'handle');
        $callbackTwo = array(new ____Callback, 'handle');

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback($callbackOne);
        $catcher->registerCallback($callbackTwo);

        $catcher->unregisterCallback($callbackTwo);

        $actualCallbacks = $this->readAttribute($catcher, 'callbacks');
        $this->assertCount(1, $actualCallbacks);
        $this->assertContains($callbackOne, $actualCallbacks);
    }

    /**
     *
     * @test
     *
     * @depends shouldAllowToRegisterCallback
     */
    public function shouldRunAllCallbacksOnExceptionHandling()
    {
        $callbackOne = $this->getMock('____Callback');
        $callbackOne->expects($this->once())->method('handle');

        $callbackTwo = $this->getMock('____Callback');
        $callbackTwo->expects($this->once())->method('handle');

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback(array($callbackOne, 'handle'));
        $catcher->registerCallback(array($callbackTwo, 'handle'));

        $catcher->handleException(new Exception('Foo'));
    }

    /**
     *
     * @test
     *
     * @depends shouldRunAllCallbacksOnExceptionHandling
     */
    public function shouldPassExceptionToCallbackOnExceptionHandling()
    {
        $expectedException = new Exception('Foo');

        $callback = $this->getMock('____Callback');
        $callback
            ->expects($this->once())
            ->method('handle')
            ->with($this->identicalTo($expectedException))
        ;

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback(array($callback, 'handle'));

        $catcher->handleException($expectedException);
    }

    /**
     *
     * @test
     *
     * @depends shouldRunAllCallbacksOnExceptionHandling
     */
    public function shouldPassErrorExceptionToCallbackOnErrorHandling()
    {
        $errorData = $this->errorData;

        $callback = $this->getMock('____Callback');
        $callback
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf('ErrorException'))
            ->will($this->returnCallback(array($this, 'callbackForShouldPassErrorExceptionToCallbackOnErrorHandling')))
        ;

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback(array($callback, 'handle'));

        $catcher->handleError($errorData['errno'], $errorData['errstr'], $errorData['errfile'], $errorData['errline']);
    }

    public function callbackForShouldPassErrorExceptionToCallbackOnErrorHandling($actualException)
    {
        $errorData = $this->errorData;

        $this->assertEquals($errorData['errstr'], $actualException->getMessage());
        $this->assertEquals(0, $actualException->getCode());
        $this->assertEquals($errorData['errno'], $actualException->getSeverity());
        $this->assertEquals($errorData['errfile'], $actualException->getFile());
        $this->assertEquals($errorData['errline'], $actualException->getLine());
    }

    /**
     *
     * @test
     *
     * @depends shouldRunAllCallbacksOnExceptionHandling
     */
    public function shouldNotRunCallbackOnCorrectShutdown()
    {
        $callback = $this->getMock('____Callback');
        $callback->expects($this->never())->method('handle');

        $catcher = $this->getMock('UniversalErrorCatcher_Catcher', array('getFatalError'));
        $catcher->expects($this->once())->method('getFatalError')->will($this->returnValue(false));

        $catcher->registerCallback(array($callback, 'handle'));

        $catcher->handleFatalError();
    }

    /**
     *
     * @test
     *
     * @depends shouldRunAllCallbacksOnExceptionHandling
     */
    public function shouldNotRunCallbackOnNoFatalShutdown()
    {
        $catcher = $this->getMock('UniversalErrorCatcher_Catcher', array('getFatalError'));
        $catcher->expects($this->once())->method('getFatalError')->will($this->returnValue($this->invalidFatalData));

        $callback = $this->getMock('____Callback');
        $callback->expects($this->never())->method('handle');

        $catcher->registerCallback(array($callback, 'handle'));

        $catcher->handleFatalError();
    }

    /**
     *
     * @test
     *
     * @depends shouldRunAllCallbacksOnExceptionHandling
     */
    public function shouldRunCallbackOnFatalShutdown()
    {
        $catcher = $this->getMock('UniversalErrorCatcher_Catcher', array('getFatalError'));
        $catcher->expects($this->once())->method('getFatalError')->will($this->returnValue($this->fatalData));

        $callback = $this->getMock('____Callback');
        $callback->expects($this->once())->method('handle');

        $catcher->registerCallback(array($callback, 'handle'));

        $catcher->handleFatalError();
    }

    /**
     * @test
     *
     * @depends shouldRunCallbackOnFatalShutdown
     */
    public function shouldRunCallbackOnFatalShutdownWithFatalErrorException()
    {
        $catcher = $this->getMock('UniversalErrorCatcher_Catcher', array('getFatalError'));
        $catcher->expects($this->once())->method('getFatalError')->will($this->returnValue($this->fatalData));

        $callback = $this->getMock('____Callback');
        $callback
            ->expects($this->atLeastOnce())
            ->method('handle')
            ->with($this->isInstanceOf('FatalErrorException'))
        ;

        $catcher->registerCallback(array($callback, 'handle'));

        $catcher->handleFatalError();
    }

    /**
     *
     * @test
     */
    public function shouldAllowToDefineWhetherToThrowRecoverableErrorAsExceptionOrNot()
    {
        $catcher = new UniversalErrorCatcher_Catcher();

        $catcher->setThrowRecoverableErrors(true);
    }

    /**
     *
     * @test
     *
     * @expectedException ErrorException
     * @expectedExceptionMessage A recoverable error has happened
     */
    public function shouldThrowRecoverableErrorIfCatherConfiguredThisWay()
    {
        $catcher = new UniversalErrorCatcher_Catcher();

        $catcher->setThrowRecoverableErrors(true);

        $catcher->handleError(E_NOTICE, 'A recoverable error has happened', __FILE__, __LINE__);
    }

    /**
     *
     * @test
     */
    public function shouldNotThrowFatalErrorIfCatherConfiguredThisWay()
    {
        $fatalData = array(
            'type' => E_ERROR,
            'message' => 10,
            'file' => 'bar.php',
            'line' => 100);

        $catcher = $this->getMock('UniversalErrorCatcher_Catcher', array('getFatalError'));
        $catcher->expects($this->once())->method('getFatalError')->will($this->returnValue($fatalData));
        $catcher->setThrowRecoverableErrors(true);

        $catcher->handleFatalError();
    }

    /**
     * @test
     */
    public function shouldCatchAllExceptionHappenedWhileHandlingException()
    {
        $exceptionWhileHandleException = new \Exception('The exception that thrown while handle exception');
        $handlingException = new \Exception('The exception that has been been processing');

        $callbackWithException = $this->getMock('stdClass', array('handle'));
        $callbackWithException
            ->expects($this->at(0))
            ->method('handle')
            ->with($handlingException)
            ->will($this->throwException($exceptionWhileHandleException))
        ;

        $successCallback = $this->getMock('stdClass', array('handle'));
        $successCallback
            ->expects($this->at(0))
            ->method('handle')
            ->with($handlingException)
        ;

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback(array($callbackWithException, 'handle'));
        $catcher->registerCallback(array($successCallback, 'handle'));

        $catcher->handleException($handlingException);
    }

    /**
     * @test
     */
    public function shouldTryHandleAllExceptionsCoughtWhileHandlingException()
    {
        $firstWhileHandleException = new \Exception('The first exception that thrown while handle the exception');
        $secondWhileHandleException = new \Exception('The second exception that thrown while handle the exception');
        $handlingException = new \Exception('The exception that must be handled');

        $firstCallbackWithException = $this->getMock('stdClass', array('handle'));
        $secondCallbackWithException = $this->getMock('stdClass', array('handle'));
        $thirdSuccessCallback = $this->getMock('stdClass', array('handle'));

        // All 3 callbacks try to handle an original exception
        $firstCallbackWithException
            ->expects($this->at(0))
            ->method('handle')
            ->with($handlingException)
            ->will($this->throwException($firstWhileHandleException))
        ;
        $secondCallbackWithException
            ->expects($this->at(0))
            ->method('handle')
            ->with($handlingException)
            ->will($this->throwException($secondWhileHandleException))
        ;
        $thirdSuccessCallback
            ->expects($this->at(0))
            ->method('handle')
            ->with($handlingException)
        ;

        // Callbacks try handle an exception that was trown by first callback during handling
        $firstCallbackWithException
            ->expects($this->at(1))
            ->method('handle')
            ->with($firstWhileHandleException)
        ;
        $secondCallbackWithException
            ->expects($this->at(1))
            ->method('handle')
            ->with($firstWhileHandleException)
        ;
        $secondCallbackWithException
            ->expects($this->at(1))
            ->method('handle')
            ->with($firstWhileHandleException)
        ;

        // Callbacks try handle an exception that was trown by second callback during handling
        $firstCallbackWithException
            ->expects($this->at(2))
            ->method('handle')
            ->with($secondWhileHandleException)
        ;
        $secondCallbackWithException
            ->expects($this->at(2))
            ->method('handle')
            ->with($secondWhileHandleException)
        ;
        $secondCallbackWithException
            ->expects($this->at(2))
            ->method('handle')
            ->with($secondWhileHandleException)
        ;

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback(array($firstCallbackWithException, 'handle'));
        $catcher->registerCallback(array($secondCallbackWithException, 'handle'));
        $catcher->registerCallback(array($thirdSuccessCallback, 'handle'));

        $catcher->handleException($handlingException);
    }

    /**
     * @test
     */
    public function shouldIgnoreAllExceptionsThrownWhileHandlingCaughtExceptions()
    {
        $firstWhileHandleException = new \Exception('The exception that thrown while handle the exception');
        $handlingException = new \Exception('The exception that must be handled');

        // The callback will throw exception every time
        $callbackWithException = $this->getMock('stdClass', array('handle'));
        $callbackWithException
            ->expects($this->any())
            ->method('handle')
            ->will($this->throwException($firstWhileHandleException))
        ;

        $successCallback = $this->getMock('stdClass', array('handle'));

        // Handle an original exception
        $successCallback
            ->expects($this->at(0))
            ->method('handle')
            ->with($handlingException)
        ;

        // Handle an exception thrown by other callback
        $successCallback
            ->expects($this->at(1))
            ->method('handle')
            ->with($firstWhileHandleException)
        ;

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback(array($callbackWithException, 'handle'));
        $catcher->registerCallback(array($successCallback, 'handle'));

        $catcher->handleException($handlingException);
    }
}

class ____Callback
{
    public function handle()
    {

    }
}