<?php

namespace Mtahv3\Hl7parser\Tests;

use InvalidArgumentException;
use Mtahv3\Hl7parser\IdentifierParser;
use PHPUnit\Framework\TestCase;

class IdentifierParserTest extends TestCase
{
    public function testItParsesNoRepeatingFieldsCorrectly()
    {
        $identifier = 'PID.1.2.3';
        $parser = new IdentifierParser($identifier);

        $this->assertEquals('PID', $parser->getSegmentName());
        $this->assertEquals(1, $parser->getFieldIndex());
        $this->assertEquals(2, $parser->getComponentIndex());
        $this->assertEquals(3, $parser->getSubComponentIndex());
    }

    public function testItParsesMissingSubComponent()
    {
        $identifier = 'PID.1.2';
        $parser = new IdentifierParser($identifier);

        $this->assertEquals('PID', $parser->getSegmentName());
        $this->assertEquals(1, $parser->getFieldIndex());
        $this->assertEquals(2, $parser->getComponentIndex());
        $this->assertNull($parser->getSubComponentIndex());
    }

    public function testItParsesMissingComponentAndSubComponent()
    {
        $identifier = 'PID.1';
        $parser = new IdentifierParser($identifier);

        $this->assertEquals('PID', $parser->getSegmentName());
        $this->assertEquals(1, $parser->getFieldIndex());
        $this->assertNull($parser->getComponentIndex());
        $this->assertNull($parser->getSubComponentIndex());
    }

    public function testItParsesRepeatedSegment()
    {
        $identifier = 'PID[4].1.2.3';
        $parser = new IdentifierParser($identifier);

        $this->assertEquals('PID', $parser->getSegmentName());
        $this->assertEquals(4, $parser->getSegmentRepeatedIndex());
    }

    public function testItParsesRepeatedFields()
    {
        $identifier = 'PID.1[6].2.3';
        $parser = new IdentifierParser($identifier);

        $this->assertEquals(1, $parser->getFieldIndex());
        $this->assertEquals(6, $parser->getFieldRepeatedIndex());
    }

    public function testItThrowsExceptionOnInvalidIdentifier()
    {
        $this->expectException(InvalidArgumentException::class);
        $identifier = 'INVALID.YES';
        new IdentifierParser($identifier);
    }

    public function testItThrowsExceptionOnlySegmentName()
    {
        $this->expectException(InvalidArgumentException::class);
        $identifier = 'PID';
        new IdentifierParser($identifier);
    }
}
