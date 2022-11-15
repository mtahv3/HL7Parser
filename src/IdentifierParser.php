<?php

namespace Mtahv3\Hl7parser;

use InvalidArgumentException;

class IdentifierParser
{
    protected string $segmentName;
    protected int $segmentRepeatedIndex;
    protected int $fieldIndex;
    protected int $fieldRepeatedIndex;
    protected ?int $componentIndex = null;
    protected ?int $subComponentIndex = null;

    public function __construct(protected string $identifier)
    {
        $this->parse();
    }

    public function getSegmentName() : string
    {
        return $this->segmentName;
    }

    public function getSegmentRepeatedIndex() : int
    {
        return $this->segmentRepeatedIndex;
    }

    public function getFieldIndex() : int
    {
        return $this->fieldIndex;
    }

    public function getFieldRepeatedIndex() : int
    {
        return $this->fieldRepeatedIndex;
    }

    public function getComponentIndex() : ?int
    {
        return $this->componentIndex ?: null;
    }

    public function getSubComponentIndex() : ?int
    {
        return $this->subComponentIndex ?: null;
    }

    protected function parse() : void
    {
        $parts = explode('.', $this->identifier);
        if (count($parts) === 1) {
            throw new InvalidArgumentException('Identifier invalid. Must contain at least a segment and field index. IE: PID.1');
        }
        [
            0 => $segment,
            1 => $field,
            2 => $component,
            3 => $subComponent,
        ] = $parts + [2 => null, 3 => null];

        $this->parseSegment($segment);
        $this->parseField($field);
        $this->parseComponent($component);
        $this->parseSubComponent($subComponent);
    }

    protected function parseSegment(string $segment) : void
    {
        if (strlen($segment) === 3) {
            $this->segmentName = $segment;
            $this->segmentRepeatedIndex = 1;
            return;
        }

        $this->segmentName = substr($segment, 0, 3);
        preg_match('/\[(.*?)\]/', $segment, $match);
        if (count($match) === 2) {
            $this->segmentRepeatedIndex = (int)$match[1];
        } else {
            throw new InvalidArgumentException('Invalid segment. Must be either 3 characters (PID) or 3 characters with the repeated index in brackets (PID[2])');
        }
    }

    protected function parseField(string $field) : void
    {
        if (is_numeric($field)) {
            $this->fieldIndex = (int)$field;
            $this->fieldRepeatedIndex = 1;
            return;
        }

        $this->fieldIndex = (int)substr($field, 0, strpos($field, '['));

        preg_match('/\[(.*?)\]/', $field, $match);
        if (count($match) === 2) {
            $this->fieldRepeatedIndex = (int)$match[1];
        } else {
            throw new InvalidArgumentException('Invalid field. Must be either numeric (1) or numeric characters with the repeated index in brackets (1[2])');
        }
    }

    protected function parseComponent(?string $component)
    {
        if (is_numeric($component)) {
            $this->componentIndex = (int) $component;
        }
    }

    protected function parseSubComponent(?string $subComponent)
    {
        if (is_numeric($subComponent)) {
            $this->subComponentIndex = (int) $subComponent;
        }
    }
}
