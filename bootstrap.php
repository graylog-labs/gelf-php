<?php


if (!file_exists("./vendor/autoload.php")) {
    die(
        "\n[ERROR] You need to run composer before running the test suite.\n".
        "To do so run the following commands:\n".
        "    curl -s http://getcomposer.org/installer | php\n".
        "    php composer.phar install\n\n"
    );
}

$loader = require_once "./vendor/autoload.php";

require_once "tests/GelfPhpTestCase.php";
