<?php

/**
 * 
 * @author Kotlyar Maksim kotlyar.maksim@gmail.com
 */
class UniversalErrorCatcher_Tests_Functional_CatcherTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    public static $scriptErrorInToString = 'scripts/ErrorInToString.php';

    /**
     * @var string
     */
    public static $scriptSuppressedWarning = 'scripts/SuppressedWarning.php';

    /**
     * @return array
     */
    public static function provideScriptsWithRecoverableErrors()
    {
        return array(
            array('scripts/Notice.php'),
            array('scripts/Warning.php'),
            array(static::$scriptSuppressedWarning)
        );
    }

    /**
     * @return array
     */
    public static function proviceScriptsWithFatalErrors()
    {
        return array(
            array('scripts/Parse.php'),
            array('scripts/Fatal.php'),
            array('scripts/FatalMemoryLimit.php'),
            array('scripts/Exception.php'),
        );
    }

    /**
     * @return array
     */
    public static function provideAllBuggyScripts()
    {
        return array_merge(
            static::provideScriptsWithRecoverableErrors(),
            static::proviceScriptsWithFatalErrors(),
            array(
                array(static::$scriptErrorInToString)
            )
        );
    }

    /**
     * @test
     *
     * @dataProvider provideAllBuggyScripts
     */
    public function shouldCatchErrorInBuggyScript($errorScript)
    {
        $r = $this->exec("php Runner.php $errorScript 2> /dev/null");

        $this->assertExecResultContainsError($r);
    }

    /**
     * @test
     */
    public function shouldNotThrowErrorWithEnabledTrhowRecoverableErrorsIfInToStringErrorOccurred()
    {
        $r = $this->exec("php RunnerThrowErrors.php " . static::$scriptErrorInToString . " 2> /dev/null");

        $this->assertExecResultContainsError($r);
    }

    /**
     * @test
     *
     * @dataProvider provideScriptsWithRecoverableErrors
     */
    public function shouldThrowErrorWithEnabledThrowRecoverableErrorsAndThrowSuppressedErrors($errorScript)
    {
        $r = $this->exec("php RunnerThrowErrors.php $errorScript 2> /dev/null");

        $this->assertErrorWasThrown($r);
    }

    /**
     * @test
     */
    public function shouldCatchErrorButNotThrowErrorWithEnabledThrowRecoverableErrorsAndDisabledThrowSuppressErrors()
    {
        $r = $this->exec("php RunnerDoesNotThrowSuppressedErrors.php " . static::$scriptSuppressedWarning . " 2> /dev/null");

        $this->assertExecResultContainsError($r);
    }

    /**
     * @test
     */
    public function shouldThrowErrorOnRecoverableErrorsWithEnabledThrowRecoverableErrorsAndEnabledThrowSuppressErrors()
    {
        $r = $this->exec("php RunnerThrowErrors.php " . static::$scriptSuppressedWarning . " 2> /dev/null");

        $this->assertSuppressedErrorWasThrown($r);
    }

    protected function exec($command)
    {
        chdir(dirname(__FILE__));
        $exitCode = 0;
        $result = array();

        exec($command, $result, $exitCode);

        return implode("\n", $result);
    }

    protected function assertExecResultContainsError($result)
    {
        $this->assertContains('The error was caught', $result, $result);
    }

    protected function assertErrorWasThrown($result)
    {
        $this->assertContains("ErrorException was thrown", $result, $result);
    }

    protected function assertSuppressedErrorWasThrown($result)
    {
        $this->assertContains("SuppressedErrorException was thrown", $result, $result);
    }
}