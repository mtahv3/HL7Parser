<?php

namespace Mtahv3\Hl7parser;

use Mtahv3\Hl7parser\Concerns\SplitsWithEscapeCharacter;
use Mtahv3\Hl7parser\Contracts\SeparatorsInterface;

class Component
{
    use SplitsWithEscapeCharacter;

    protected array $subComponents = [];
    protected bool $hasSubComponent = false;
    protected ?string $value = null;
    protected string $identifier = '';

    public function __construct(protected $componentString, protected ?SeparatorsInterface $separators = null)
    {
        if (!$this->separators) {
            $this->separators = new DefaultSeparators;
        }

        $this->parse();
    }

    public function getSeparators() : SeparatorsInterface
    {
        return $this->separators;
    }

    public function getRawString() : string
    {
        return $this->componentString;
    }

    public function getSubComponent(int $index) : SubComponent
    {
        return $this->subComponents[$index];
    }

    public function getIdentifier() : string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier) : void
    {
        $this->identifier = $identifier;
        $this->generateIdentifiers();
    }

    public function value() : ?string
    {
        if ($this->value) {
            return $this->value;
        }

        if (count($this->subComponents)) {
            return reset($this->subComponents)->value();
        }

        return null;
    }

    protected function generateIdentifiers() : void
    {
        foreach ($this->subComponents as $i => $subComponent) {
            /** @var SubComponent $subComponent */
            $subComponent->setIdentifier($this->identifier . '.' . $i);
        }
    }

    protected function parse() : void
    {
        $subComponents = $this->splitEscaped($this->componentString, $this->separators->getSubComponentSeparator(), $this->separators->getEscapeCharacter());

        if (count($subComponents) === 1) {
            $this->value = $subComponents[0];
            return;
        }

        foreach ($subComponents as $i => $subComponent) {
            $this->subComponents[$i + 1] = new SubComponent($subComponent);
        }
    }
}
