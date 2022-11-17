<?php

namespace Mtahv3\Hl7parser;

use Mtahv3\Hl7parser\Contracts\SeparatorsInterface;
use Mtahv3\Hl7parser\Exceptions\HL7MessageException;

class MessageHeader extends Segment implements SeparatorsInterface
{
    const MSH = 'MSH';

    protected string $fieldSeparator = '|';
    protected string $escapeCharacter = '\\';
    protected string $componentSeparator = '^';
    protected string $subComponentSeparator = '&';
    protected string $repetitionSeparator = '~';

    public function __construct(string $segmentString)
    {
        $this->parseFieldSeparator($segmentString);
        parent::__construct($segmentString, $this);
        $this->validateHeader();
        $this->parseSeparators();
        $this->setIdentifier('MSH');
    }

    public function getDateTimeOfMessage() : string
    {
        return $this->getField(7)->getComponent(1)->value();
    }

    public function getMessageControlId() : string
    {
        return $this->getField(10)->getComponent(1)->value();
    }

    public function getMessageType() : string
    {
        return $this->getField(9)->getComponent(1)->value();
    }

    public function getTriggerEvent() : string
    {
        return $this->getField(9)->getComponent(2)->value();
    }

    public function getSendingApplication() : string
    {
        return $this->getField(3)->getComponent(1)->value();
    }

    public function getSendingFacility() : string
    {
        return $this->getField(4)->getComponent(1)->value();
    }

    public function getFieldSeparator() : string
    {
        return $this->fieldSeparator;
    }

    public function getEscapeCharacter() : string
    {
        return $this->escapeCharacter;
    }

    public function getComponentSeparator() : string
    {
        return $this->componentSeparator;
    }

    public function getSubComponentSeparator() : string
    {
        return $this->subComponentSeparator;
    }

    public function getRepetitionSeparator() : string
    {
        return $this->repetitionSeparator;
    }

    protected function parseFieldSeparator(string $segmentString) : void
    {
        $this->fieldSeparator = substr($segmentString, 3, 1);
    }

    protected function parseSeparators() : void
    {
        $separatorsString = $this->getField(2)->getRawString();
        [
            $this->componentSeparator,
            $this->repetitionSeparator,
            $this->escapeCharacter,
            $this->subComponentSeparator
        ] = str_split($separatorsString);
    }

    protected function validateHeader() : void
    {
        try {
            $this->validateSegment(self::MSH);
        } catch(HL7MessageException $e) {
            throw new HL7MessageException('Invalid HL7 message. First segment must be a ' . self::MSH . ' segment');
        }
    }
}
