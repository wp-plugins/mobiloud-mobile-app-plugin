<?php
require_once(dirname( __FILE__ ) . "/raven/Client.php");
require_once(dirname( __FILE__ ) . "/raven/Compat.php");
require_once(dirname( __FILE__ ) . "/raven/ErrorHandler.php");
require_once(dirname( __FILE__ ) . "/raven/Processor.php");
require_once(dirname( __FILE__ ) . "/raven/SanitizeDataProcessor.php");
require_once(dirname( __FILE__ ) . "/raven/Serializer.php");
require_once(dirname( __FILE__ ) . "/raven/Stacktrace.php");
require_once(dirname( __FILE__ ) . "/raven/Util.php");



$client = new Raven_Client('https://88851c4dc3394929aebf08417559bb1e:a810ccf7710f4553aaf0525b2594b50e@app.getsentry.com/12223');

$error_handler = new Raven_ErrorHandler($client);
$error_handler->registerExceptionHandler();
$error_handler->registerErrorHandler();
$error_handler->registerShutdownFunction();
?>