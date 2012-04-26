<?php

namespace Gelf;

interface IMessagePublisher {
    /**
     * Publishes a Message, returns false if an error occured during write
     *
     * @throws UnexpectedValueException
     * @param unknown_type $message
     * @return boolean
     */
    public function publish(Message $message);
}
