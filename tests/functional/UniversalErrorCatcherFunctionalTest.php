<?php

/**
 * 
 * @author Kotlyar Maksim kotlyar.maksim@gmail.com
 */
class UniversalErrorCatcherFunctionalTest extends PHPUnit_Framework_TestCase
{
    public static function provideBuggyScripts()
    {
        return array(
            array('scripts/notice.php'),
            array('scripts/parse.php'),
            array('scripts/fatal.php'),
            array('scripts/fatal_memory_limit.php'),
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
        $r = $this->execBuggyScript($errorScript);

        $this->assertExecResultContainsError($r);
    }

    protected function execBuggyScript($errorFile)
    {
        chdir(__DIR__);
        $exitCode = 0;
        $result = array();

        exec("php runner.php $errorFile 2> /dev/null", $result, $exitCode);
        
        return implode("\n", $result);
    }

    protected function assertExecResultContainsError($result)
    {
        $this->assertContains('The error was catched', $result, $result);
    }
}