<?php

namespace Mtahv3\Hl7parser\Tests;

use Mtahv3\Hl7parser\Exceptions\HL7MessageException;
use Mtahv3\Hl7parser\Segment;
use PHPUnit\Framework\TestCase;

class SegmentTest extends TestCase
{
    protected $validSegment = 'MSH|1|testing|more^and^more';
    protected $invalidSegmentShort = 'MS|1|testing|more^and^more';
    protected $invalidSegmentLong = 'MASH|1|testing|more^and^more';

    public function testSegmentsThatAreTooShortThrowExceptions()
    {
        $this->expectException(HL7MessageException::class);
        new Segment($this->invalidSegmentShort);
    }

    public function testSegmentsThatAreTooLongThrowExceptions()
    {
        $this->expectException(HL7MessageException::class);
        new Segment($this->invalidSegmentLong);
    }

    public function testNonExistentFieldsReturnNull()
    {
        $segment = new Segment($this->validSegment);

        $this->assertNull($segment->getField(1000000)->value());
    }
}
