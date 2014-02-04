<?php

class GelfMessageTest extends GelfPhpTestCase
{
    public function testGelfMessage()
    {
        $time = time();
        $message = $this->getGelfMessage($time);

        $this->assertEquals(
            array(
                'version' => null,
                'timestamp' => null,
                'short_message' => 'something is broken.',
                'full_message' => 'lol full message!',
                'facility' => null,
                'host' => 'somehost',
                'level' => GELFMessage::CRITICAL,
                'file' => '/var/www/example.php',
                'line' => 1337,
                '_something' => 'foo',
                '_something_else' => 'bar',
                'timestamp' => $time,
                'facility' => 'someFacitlity',
            ),
            $message->toArray()
        );
    }

    public function testAdditionalField() {
        $message = new GELFMessage(time());
        $message->setAdditional("something", "foo");
        $this->assertEquals("foo", $message->getAdditional("something"));
    }
}

