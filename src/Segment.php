<?php

namespace Mtahv3\Hl7parser;

use Mtahv3\Hl7parser\Concerns\SplitsWithEscapeCharacter;
use Mtahv3\Hl7parser\Contracts\SeparatorsInterface;
use Mtahv3\Hl7parser\Exceptions\HL7MessageException;

class Segment
{
    use SplitsWithEscapeCharacter;

    const SEGMENT_NAME_LENGTH = 3;

    protected array $fields = [];
    protected string $name;
    protected int $repeatingIndex = 1;
    protected string $identifier;

    public function __construct(protected $segmentString, protected ?SeparatorsInterface $separators = null)
    {
        if (!$this->separators) {
            $this->separators = new DefaultSeparators;
        }
        $this->validateSegment();
        $this->parse();
    }

    public function getSeparators() : SeparatorsInterface
    {
        return $this->separators;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getFields() : array
    {
        return $this->fields;
    }

    public function getField(int $index) : Field
    {
        if (isset($this->fields[$index])) {
            return $this->fields[$index];
        }
        return new Field('', $this->separators);
    }

    public function getRawString() : string
    {
        return $this->segmentString;
    }

    public function getRepeatingIndex() : int
    {
        return $this->repeatingIndex;
    }

    public function setRepeatingIndex(int $index) : void
    {
        $this->repeatingIndex = $index;
    }

    public function setIdentifier(string $identifier) : void
    {
        $this->identifier = $identifier;
        $this->generateIdentifiers();
    }

    protected function parse() : void
    {
        $fields = $this->splitEscaped($this->segmentString, $this->separators->getFieldSeparator(), $this->separators->getEscapeCharacter());

        foreach ($fields as $i => $field) {
            if ($i === 0) {
                $this->name = $field;
                if ($this->name === MessageHeader::MSH) {
                    $this->fields[] = new Field($field, $this->separators);
                    $this->fields[] = new Field('|', $this->separators);
                    continue;
                }
            }
            $this->fields[] = new Field($field, $this->separators);
        }
    }

    protected function generateIdentifiers()
    {
        foreach ($this->fields as $i => $field) {
            /** @var Field $field */
            $field->setIdentifier($this->identifier . '.' . $i);
        }
    }

    protected function validateSegment(?string $checkName = null) : void
    {
        $segmentName = substr($this->segmentString, 0, (self::SEGMENT_NAME_LENGTH));

        if ($checkName && $segmentName !== $checkName) {
            throw new HL7MessageException('Segment name of "' . $segmentName . '" did not match the check of "' . $checkName . '".');
        }

        if (strtoupper($segmentName . $this->separators->getFieldSeparator()) !==
            substr($this->segmentString, 0, (self::SEGMENT_NAME_LENGTH + 1))) {
            throw new HL7MessageException('Invalid HL7 message. Segments must start with a 3 character, uppercase segment name.');
        }
    }
}
