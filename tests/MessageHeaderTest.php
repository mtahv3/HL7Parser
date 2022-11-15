<?php

namespace Mtahv3\Hl7parser\Tests;

use Mtahv3\Hl7parser\MessageHeader;
use PHPUnit\Framework\TestCase;

class MessageHeaderTest extends TestCase
{
    protected $mshSegment = 'MSH|^~\&|EPIC|EPICADT|SMS|SMSADT|199912271408|CHARRIS|ADT^A04|1817457|D|2.5|';

    protected function getMessageHeader() : MessageHeader
    {
        return new MessageHeader($this->mshSegment);
    }

    public function testMessageHeaderExtractsSeparators()
    {
        $messageHeader = $this->getMessageHeader();

        $this->assertEquals('^', $messageHeader->getComponentSeparator());
        $this->assertEquals('~', $messageHeader->getRepetitionSeparator());
        $this->assertEquals('\\', $messageHeader->getEscapeCharacter());
        $this->assertEquals('&', $messageHeader->getSubComponentSeparator());
    }

    public function testItReturnsTheCorrectControlId()
    {
        $this->assertEquals('1817457', $this->getMessageHeader()->getMessageControlId());
    }

    public function testItReturnsTheCorrectDateTime()
    {
        $this->assertEquals('199912271408', $this->getMessageHeader()->getDateTimeOfMessage());
    }

    public function testItReturnsTheMessageType()
    {
        $this->assertEquals('ADT', $this->getMessageHeader()->getMessageType());
    }

    public function testItReturnsTheTriggerEvent()
    {
        $this->assertEquals('A04', $this->getMessageHeader()->getTriggerEvent());
    }
}
