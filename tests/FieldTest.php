<?php

namespace Mtahv3\Hl7parser\Tests;

use Mtahv3\Hl7parser\Field;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    protected string $repeatedFieldString = 'DUCK^DONALD^D~ALLEN^MATT^G';
    protected string $fieldString = 'ALLEN^MATT^G';

    public function testItParsesRepeatedFields()
    {
        $field = new Field($this->repeatedFieldString);

        $this->assertTrue($field->isRepeated());

        $this->assertEquals('DONALD', $field->getComponent(2, 1)->value());
        $this->assertEquals('MATT', $field->getComponent(2, 2)->value());
    }

    public function testItDefaultsFieldValueToFirstComponent()
    {
        $field = new Field($this->fieldString);

        $this->assertEquals('ALLEN', $field->value());
    }

    public function testItReturnsNullForANonExistentComponent()
    {
        $field = new Field($this->fieldString);

        $this->assertNull($field->getComponent(10000000)->value());
    }
}
