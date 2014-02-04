<?php

class GelfMessagePublisherTest extends GelfPhpTestCase
{
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

    /**
     * @dataProvider provideFalseConfiguration
     */
    public function testConstructorExpectingException(array $conf)
    {
        $this->setExpectedException('InvalidArgumentException');
        $publisher = new GELFMessagePublisher($conf['host'], $conf['port'], $conf['chunkSize']);
    }
    public function provideFalseConfiguration()
    {
        return array(
            'no hostmane' =>array(array('host'=>'', 'port' => 12201, 'chunkSize' => 1420)),
            'null value hostmane' =>array(array('host'=>null, 'port' => 12201, 'chunkSize' => 1420)),
            'boolean hostmane' =>array(array('host'=>false, 'port' => 12201, 'chunkSize' => 1420)),
            'whitespaces as hostmane' =>array(array('host'=>'    ', 'port' => 12201, 'chunkSize' => 1420)),
            'non numeric port' =>array(array('host'=>'localhost', 'port' => 'invalid Port', 'chunkSize' => 1420)),
            'invalid chunkSize' =>array(array('host'=>'localhost', 'port' => 12201, 'chunkSize' => 'invalid chunkSize')),
        );
    }

    public function testPublishInChunksErrorWhileWritingToSocket()
    {
        $conf = $this->getConfiguration();
        $publisher = new GELFMessagePublisher($conf['host'], $conf['port'], 5);

        $this->setExpectedException('RuntimeException');
        $publisher->publish($this->getGelfMessage(time()));
    }

    public function testPublishErrorWhileWritingToSocket()
    {
        $conf = $this->getConfiguration();
        $publisher = new GELFMessagePublisher($conf['host'], $conf['port'], $conf['chunkSize']);

        $this->setExpectedException('RuntimeException');
        $publisher->publish($this->getGelfMessage(time()));
    }

    public function testPublishInvalidMessage()
    {
        $conf = $this->getConfiguration();
        $publisher = new GELFMessagePublisher($conf['host'], $conf['port'], $conf['chunkSize']);

        $this->setExpectedException('UnexpectedValueException');
        $publisher->publish(new GELFMessage());
    }
}

