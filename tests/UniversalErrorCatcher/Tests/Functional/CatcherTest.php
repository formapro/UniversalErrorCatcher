<?php

/**
 * 
 * @author Kotlyar Maksim kotlyar.maksim@gmail.com
 */
class UniversalErrorCatcher_Tests_Functional_CatcherTest extends PHPUnit_Framework_TestCase
{
    public static function provideBuggyScripts()
    {
        return array(
            array('scripts/notice.php'),
            array('scripts/parse.php'),
            array('scripts/fatal.php'),
            array('scripts/fatal_memory_limit.php'),
            array('scripts/error_in_to_string.php'),
            array('scripts/exception.php')
        );
    }

    /**
     *
     * @test
     *
     * @dataProvider provideBuggyScripts
     */
    public function shouldCatchErrorInBuggyScript($errorScript)
    {
        $r = $this->exec("php runner.php $errorScript 2> /dev/null");

        $this->assertExecResultContainsError($r);
    }

    /**
     *
     * @test
     *
     * @dataProvider provideBuggyScripts
     */
    public function shouldCatchErrorInBuggyScriptWithErrorThrowing($errorScript)
    {
        $r = $this->exec("php runner_throw_errors.php $errorScript 2> /dev/null");

        $this->assertExecResultContainsError($r);
    }

    protected function exec($command)
    {
        chdir(__DIR__);
        $exitCode = 0;
        $result = array();

        exec($command, $result, $exitCode);

        return implode("\n", $result);
    }

    protected function assertExecResultContainsError($result)
    {
        $this->assertContains('The error was caught', $result, $result);
    }
}