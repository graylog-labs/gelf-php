<?php

class GelfMessagePublisherTest extends GelfPhpTestCase
{
    /**
     * @covers \GelfMessaggePublisher::publish
     */
    public function testPublish()
    {
        $conf = $this->getConfiguration();
        $publisher = new GELFMessagePublisher($conf['host'], $conf['port'], $conf['chunkSize']);

        $this->assertNull($publisher->publish($this->getGelfMessage()));
    }

    /**
     * @covers \GelfMessaggePublisher::getSocketConnection
     */
    public function testGetSocketConnection()
    {
        $host = 'localhost';
        $publisher = $this->getProxyBuilder('\GelfMessagePublisher')
            ->setConstructorArgs(array($host))
            ->setMethods(array('getSocketConnection'))
            ->getProxy();

        $this->assertInternalType('resource', $publisher->getSocketConnection());
    }

    public function testGetSocketConnectionExpectingException()
    {
        $host = 'InvalidHost';
        $publisher = $this->getProxyBuilder('\GelfMessagePublisher')
            ->setConstructorArgs(array($host))
            ->setMethods(array('getSocketConnection'))
            ->getProxy();

        $this->setExpectedException('\RuntimeException');
        $publisher->getSocketConnection();
    }
}

