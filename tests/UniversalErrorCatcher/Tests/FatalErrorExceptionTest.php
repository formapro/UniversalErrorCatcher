<?php

class UniversalErrorCatcher_Tests_FatalErrorExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldProvideSetOfFatalErrorCodes()
    {
        $this->assertEquals(
            array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR),
            FatalErrorException::getFatalCodes()
        );
    }
}