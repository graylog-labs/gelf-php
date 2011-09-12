<?php
require('gelfErrorHandler.php');

define('APP_NAME', 'Hello graylog');
define('GRAYLOG2_HOST', 'localhost');
define('GARYLOG2_PORT', 12201);


$gelf = new gelfErrorHandler(GRAYLOG2_HOST, GRAYLOG2_PORT);
set_error_handler(array($gelf, 'handler'));

trigger_error("Something went completely wrong :(", E_USER_ERROR);
