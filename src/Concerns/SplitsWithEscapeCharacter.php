<?php

namespace Mtahv3\Hl7parser\Concerns;

trait SplitsWithEscapeCharacter
{
    public function splitEscaped(string $string, string $separator, string $escapeCharacter) : array
    {
        return preg_split('~\\\\.(*SKIP)(*FAIL)|' . $escapeCharacter . $separator . '~s', $string);
    }
}
