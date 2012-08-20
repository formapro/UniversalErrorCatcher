<?php

class ToStringTest
{
    public function __toString()
    {
        return '' . $a;
    }
}

$toStringTest = new ToStringTest();

echo $toStringTest;