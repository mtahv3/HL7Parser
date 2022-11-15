<?php

namespace Mtahv3\Hl7parser\Contracts;

interface SeparatorsInterface
{
    public function getFieldSeparator() : string;

    public function getComponentSeparator() : string;

    public function getSubComponentSeparator() : string;

    public function getEscapeCharacter() : string;

    public function getRepetitionSeparator() : string;
}
