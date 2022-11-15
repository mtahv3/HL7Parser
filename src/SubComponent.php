<?php

namespace Mtahv3\Hl7parser;

class SubComponent
{
    protected string $identifier;

    public function __construct(protected string $value)
    {
    }

    public function setIdentifier(string $identifier) : void
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier() : string
    {
        return $this->identifier;
    }

    public function value() : ?string
    {
        if ($this->value) {
            return $this->value;
        }
        return null;
    }
}
