<?php
use lapistano\ProxyObject\ProxyBuilder;

/**
 * Created by JetBrains PhpStorm.
 * User: lapistano
 * Date: 8/3/13
 * Time: 11:26 AM
 * To change this template use File | Settings | File Templates.
 */

class GelfPhpTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Provides an instace of the ProxyBuilder class.
     *
     * @param string $className
     *
     * @return ProxyBuilder
     */
    public function getProxyBuilder($className)
    {
        return new ProxyBuilder($className);
    }

    /**
     * Provides a default message object.
     *
     * @return GELFMessage
     */
    protected function getGelfMessage()
    {
        $message = new GELFMessage();
        $message->setShortMessage('something is broken.');
        $message->setFullMessage("lol full message!");
        $message->setHost('somehost');
        $message->setLevel(GELFMessage::CRITICAL);
        $message->setFile('/var/www/example.php');
        $message->setLine(1337);
        $message->setAdditional("something", "foo");
        $message->setAdditional("something_else", "bar");

        return $message;
    }

    /**
     * Provides a default configuration for the GelfMessagePubisher.
     * @return array
     */
    protected function getConfiguration()
    {
        return array(
            'host' => 'localhost',
            'port' => GELFMessagePublisher::GRAYLOG2_DEFAULT_PORT,
            'chunkSize' => GELFMessagePublisher::CHUNK_SIZE_WAN
        );
    }
}
