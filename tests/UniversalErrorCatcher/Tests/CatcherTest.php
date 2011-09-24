<?php

/**
 * 
 * @author Kotlyar Maksim kotlyar.maksim@gmail.com
 */
class UniversalErrorCatcher_Tests_CatcherTest extends PHPUnit_Framework_TestCase
{
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
        $callback = function() {};

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
        $callbackOne = function() {};
        $callbackTwo = function() {};

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback($callbackOne);
        $catcher->registerCallback($callbackTwo);

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
        $callbackOne = function() {};
        $callbackTwo = function() {};

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback($callbackOne);
        $catcher->registerCallback($callbackTwo);

        $catcher->unregisterCallback($callbackTwo);

        $this->assertAttributeContains($callbackOne, 'callbacks', $catcher);
        $this->assertAttributeNotContains($callbackTwo, 'callbacks', $catcher);
    }
    
    /**
     *
     * @test
     *
     * @depends shouldAllowToRegisterCallback
     */
    public function shouldRunAllCallbacksOnExceptionHandling()
    {
        $checker = new stdClass;
        $checker->calledOne = false;
        $checker->calledTwo = false;

        $callbackOne = function() use($checker) {
            $checker->calledOne = true;
        };

        $callbackTwo = function() use($checker) {
            $checker->calledTwo = true;
        };

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback($callbackOne);
        $catcher->registerCallback($callbackTwo);

        $catcher->handleException(new Exception('Foo'));

        $this->assertTrue($checker->calledOne);
        $this->assertTrue($checker->calledTwo);
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
        $testcase = $this;

        $callback = function($actualException) use($testcase, $expectedException) {
            $testcase->assertSame($expectedException, $actualException);
        };

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback($callback);

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
        $errorData = array(
            'errstr' => 'foo', 
            'errno' => 10, 
            'errfile' => 'bar.php', 
            'errline' => 100);
        $testcase = $this;

        $callback = function($actualException) use($testcase, $errorData) {
            $testcase->assertInstanceOf('ErrorException', $actualException);

            $testcase->assertEquals($errorData['errstr'], $actualException->getMessage());
            $testcase->assertEquals(0, $actualException->getCode());
            $testcase->assertEquals($errorData['errno'], $actualException->getSeverity());
            $testcase->assertEquals($errorData['errfile'], $actualException->getFile());
            $testcase->assertEquals($errorData['errline'], $actualException->getLine());
        };

        $catcher = new UniversalErrorCatcher_Catcher();
        $catcher->registerCallback($callback);

        $catcher->handleError($errorData['errno'], $errorData['errstr'], $errorData['errfile'], $errorData['errline']);
    }

    /**
     *
     * @test
     *
     * @depends shouldRunAllCallbacksOnExceptionHandling
     */
    public function shouldNotRunCallbackOnCorrectShutdown()
    {
        $catcher = $this->getMock('UniversalErrorCatcher_Catcher', array('getFatalError'));
        $catcher->expects($this->once())->method('getFatalError')->will($this->returnValue(false));

        $checker = new stdClass();
        $checker->nevercalled = true;
        $testcase = $this;

        $callback = function() use($testcase, $checker) {
            $checker->nevercalled = false;
        };

        $catcher->registerCallback($callback);

        $catcher->handleFatalError();

        $this->assertTrue($checker->nevercalled);
    }

    /**
     *
     * @test
     *
     * @depends shouldRunAllCallbacksOnExceptionHandling
     */
    public function shouldNotRunCallbackOnNoFatalShutdown()
    {
        $fatalData = array(
            'type' => 'foo',
            'message' => 10,
            'file' => 'bar.php',
            'line' => 100);

        //guard
        $this->assertNotContains($fatalData['type'], UniversalErrorCatcher_ErrorCode::getFatals());

        $catcher = $this->getMock('UniversalErrorCatcher_Catcher', array('getFatalError'));
        $catcher->expects($this->once())->method('getFatalError')->will($this->returnValue($fatalData));

        $checker = new stdClass();
        $checker->nevercalled = true;
        $testcase = $this;

        $callback = function() use($checker) {
            $checker->nevercalled = false;
        };

        $catcher->registerCallback($callback);

        $catcher->handleFatalError();

        $this->assertTrue($checker->nevercalled);
    }

    /**
     *
     * @test
     *
     * @depends shouldRunAllCallbacksOnExceptionHandling
     */
    public function shouldRunCallbackOnFatalShutdown()
    {
        $fatalData = array(
            'type' => E_ERROR,
            'message' => 10,
            'file' => 'bar.php',
            'line' => 100);

        //guard

        $this->assertContains($fatalData['type'], UniversalErrorCatcher_ErrorCode::getFatals());

        $catcher = $this->getMock('UniversalErrorCatcher_Catcher', array('getFatalError'));
        $catcher->expects($this->once())->method('getFatalError')->will($this->returnValue($fatalData));

        $checker = new stdClass();
        $checker->called = false;
        $testcase = $this;

        $callback = function() use($checker) {
            $checker->called = true;
        };

        $catcher->registerCallback($callback);

        $catcher->handleFatalError();

        $this->assertTrue($checker->called);
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
    public function shoulThrowRecoverableErrorIfCatherConfiguredThisWay()
    {
        $catcher = new UniversalErrorCatcher_Catcher();

        $catcher->setThrowRecoverableErrors(true);

        $catcher->handleError(E_NOTICE, 'A recoverable error has happened', __FILE__, __LINE__);
    }
}