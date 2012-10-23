<?php

class FatalErrorException extends UniversalErrorCatcher_ErrorException
{
    /**
     * @return array
     */
    public static function getFatalCodes()
    {
        return array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR);
    }
}
