<?php

namespace Mtahv3\Hl7parser\Tests;

use Mtahv3\Hl7parser\Exceptions\HL7MessageException;
use Mtahv3\Hl7parser\Message;
use Mtahv3\Hl7parser\Tests\Concerns\UsesMessages;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    use UsesMessages;

    public function testItSplitsLinesIntoSegmentsAndDoesNotIncludeMshInSegmentCount()
    {
        $adt = $this->getAdtMessage();
        $lines = explode(PHP_EOL, $adt);
        $message = new Message($adt);
        $this->assertEquals(count($lines) - 1, count($message->getSegments()));
    }

    public function testMessagesMustStartWithMSHSegment()
    {
        $this->expectException(HL7MessageException::class);

        $adt = $this->getAdtMessage();
        $lines = explode(PHP_EOL, $adt);
        unset($lines[0]); //remove MSH segment
        $adt = implode(PHP_EOL, $lines);
        new Message($adt);
    }

    public function testItParsesRepeatedSegments()
    {
        $message = new Message($this->getAdtMessage());

        $nk1Segments = $message->getSegmentsByName('NK1');
        $this->assertEquals(2, count($nk1Segments));
    }

    public function testItReturnsRepeatedSegmentsByIndex()
    {
        $message = new Message($this->getAdtMessage());
        $nk1 = $message->getSegmentByName('NK1', 2);

        $this->assertEquals('NK1||ROE^MARK^^^^|SPO&SPOUSE||(314)123-4567||EC|||||||||||||||||||||||||||', $nk1->getRawString());
    }

    public function testItCanHandleNonPipedMessages()
    {
        $message = new Message($this->getNonPipeMessage());

        $this->assertEquals('*', $message->getMessageHeader()->getFieldSeparator());
        $this->assertEquals('0493575', $message->getSegmentByName('PID')->getField(2)->getComponent(1)->value());
    }

    public function testItGeneratesIdentifiersCorrectly()
    {
        $message = new Message($this->getAdtMessage());
        $this->assertEquals('PID.2.1', $message->getSegmentByName('PID')->getField(2)->getComponent(1)->getIdentifier());

        $this->assertEquals('NK1[2].2.2', $message->getSegmentByName('NK1', 2)->getField(2)->getComponent(2)->getIdentifier());

        $this->assertEquals('PID.18[2].1', $message->getSegmentByName('PID')->getField(18)->getComponent(1, 2)->getIdentifier());

        $this->assertEquals('NK1[1].3.1.2', $message->getSegmentByName('NK1')->getField(3)->getComponent(1)->getSubComponent(2)->getIdentifier());
    }

    public function testItReturnsFirstComponentForFieldValue()
    {
        $message = new Message($this->getAdtMessage());

        $this->assertEquals('19480203', $message->getSegmentByName('PID')->getField(7)->value());
    }

    public function testItCanReturnValueByIdentifier()
    {
        $message = new Message($this->getAdtMessage());

        $this->assertEquals('19480203', $message->getValueByIdentifier('PID.7'));
        $this->assertEquals('19480203', $message->getValueByIdentifier('PID.7.1'));
        $this->assertEquals('MARK', $message->getValueByIdentifier('NK1[2].2.2'));
        $this->assertEquals('SPOUSE', $message->getValueByIdentifier('NK1.3.1.2'));
        $this->assertEquals('JOHN', $message->getValueByIdentifier('PID.5.2'));
        $this->assertEquals('1129086', $message->getValueByIdentifier('PID.18[2].1'));
        $this->assertEquals('400003403', $message->getValueByIdentifier('PID.18[1].1'));
    }
}
