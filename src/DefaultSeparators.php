<?php

namespace Mtahv3\Hl7parser;

use Mtahv3\Hl7parser\Contracts\SeparatorsInterface;

class DefaultSeparators implements SeparatorsInterface
{
    public function getFieldSeparator() : string
    {
        return '|';
    }

    public function getEscapeCharacter() : string
    {
        return '\\';
    }

    public function getComponentSeparator() : string
    {
        return '^';
    }

    public function getSubComponentSeparator() : string
    {
        return '@';
    }

    public function getRepetitionSeparator() : string
    {
        return '~';
    }
}
