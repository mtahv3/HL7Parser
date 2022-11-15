<?php

namespace Mtahv3\Hl7parser;

class Message
{
    protected array $segments = [];

    protected MessageHeader $messageHeader;

    public function __construct(protected string $messageString)
    {
        $this->parse();
        $this->generateIdentifiers();
    }

    public function getSegments() : array
    {
        return array_merge(...array_values($this->segments));
    }

    public function getSegmentByName(string $segmentName, int $index = 1) : ?Segment
    {
        if (isset($this->segments[$segmentName], $this->segments[$segmentName][$index - 1])) {
            return $this->segments[$segmentName][$index - 1];
        }

        return null;
    }

    public function getSegmentsByName(string $segmentName) : array
    {
        return $this->segments[$segmentName] ?: [];
    }

    public function getMessageHeader() : MessageHeader
    {
        return $this->messageHeader;
    }

    public function getRawString() : string
    {
        return $this->messageString;
    }

    public function generateAck() : ACK
    {
    }

    public function getValueByIdentifier(string $identifier) : ?string
    {
        $parser = new IdentifierParser($identifier);
        $value = $this->getSegmentByName($parser->getSegmentName(), $parser->getSegmentRepeatedIndex())
            ->getField($parser->getFieldIndex());

        if ($parser->getComponentIndex()) {
            $value = $value->getComponent($parser->getComponentIndex(), $parser->getFieldRepeatedIndex());
            if ($parser->getSubComponentIndex()) {
                $value = $value->getSubComponent($parser->getSubComponentIndex());
            }
        }

        return $value->value();
    }

    protected function parse() : void
    {
        $segmentLines = preg_split('/(\n)/', $this->messageString);
        $this->messageHeader = new MessageHeader($segmentLines[0]);
        foreach ($segmentLines as $i => $segmentString) {
            //ignore header line
            if ($i !== 0) {
                $segment = new Segment($segmentString, $this->messageHeader);
                $this->segments[$segment->getName()][] = $segment;
                $segment->setRepeatingIndex(count($this->segments[$segment->getName()]));
            }
        }
    }

    protected function generateIdentifiers()
    {
        foreach ($this->segments as $segmentName => $segments) {
            $repeatingSegment = false;
            if (count($segments) > 1) {
                $repeatingSegment = true;
            }
            foreach ($segments as $segment) {
                /** @var Segment $segment */
                $identifier = $segmentName;
                if ($repeatingSegment) {
                    $identifier .= '[' . $segment->getRepeatingIndex() . ']';
                }
                $segment->setIdentifier($identifier);
            }
        }
    }
}
