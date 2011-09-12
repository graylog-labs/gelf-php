<?php
require('gelfErrorHandler.php');

$gelf = new gelfErrorHandler('88.87.57.5', 12201);
set_error_handler(array($gelf, 'handler'));


trigger_error("hehehehe");

