<?php

namespace Mtahv3\Hl7parser;

use Mtahv3\Hl7parser\Concerns\SplitsWithEscapeCharacter;
use Mtahv3\Hl7parser\Contracts\SeparatorsInterface;

class Field
{
    use SplitsWithEscapeCharacter;

    protected $components = [];
    protected bool $isRepeated = false;
    protected string $identifier;

    public function __construct(protected $fieldString, protected ?SeparatorsInterface $separators = null)
    {
        if (!$this->separators) {
            $this->separators = new DefaultSeparators;
        }

        $this->parse();
    }

    public function getComponent(int $index, int $repeatedIndex = 1) : Component
    {
        if (isset($this->components[$repeatedIndex], $this->components[$repeatedIndex][$index])) {
            return $this->components[$repeatedIndex][$index];
        }

        return new Component('', $this->separators);
    }

    public function getRawString() : string
    {
        return $this->fieldString;
    }

    public function isRepeated() : bool
    {
        return $this->isRepeated;
    }

    public function setIdentifier(string $identifier) : void
    {
        $this->identifier = $identifier;
        $this->generateIdentifiers();
    }

    public function value() : ?string
    {
        return $this->getComponent(1)->value();
    }

    protected function generateIdentifiers() : void
    {
        foreach ($this->components as $repeatingIndex => $components) {
            $identifier = $this->identifier;
            if ($this->isRepeated()) {
                $identifier .= '[' . $repeatingIndex . ']';
            }
            foreach ($components as $i => $component) {
                /** @var Component $component */
                $component->setIdentifier($identifier . '.' . $i);
            }
        }
    }

    protected function parse()
    {
        $repeatedFields = $this->splitEscaped($this->fieldString, $this->separators->getRepetitionSeparator(), $this->separators->getEscapeCharacter());

        if (count($repeatedFields) > 1) {
            $this->isRepeated = true;
        }

        foreach ($repeatedFields as $repeatedIndex => $field) {
            $components = $this->splitEscaped($field, $this->separators->getComponentSeparator(), $this->separators->getEscapeCharacter());

            foreach ($components as $i => $component) {
                $this->components[$repeatedIndex + 1][$i + 1] = new Component($component, $this->separators);
            }
        }
    }
}
