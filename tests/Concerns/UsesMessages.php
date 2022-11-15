<?php

namespace Mtahv3\Hl7parser\Tests\Concerns;

trait UsesMessages
{
    protected function getAdtMessage() : string
    {
        return file_get_contents(__DIR__ . '/../messages/adt.txt');
    }

    protected function getNonPipeMessage() : string
    {
        return file_get_contents(__DIR__ . '/../messages/adtnonpipe.txt');
    }
}
