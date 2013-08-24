<?php
require_once(dirname( __FILE__ ) . "/raven/Client.php");
require_once(dirname( __FILE__ ) . "/raven/Compat.php");
require_once(dirname( __FILE__ ) . "/raven/ErrorHandler.php");
require_once(dirname( __FILE__ ) . "/raven/Processor.php");
require_once(dirname( __FILE__ ) . "/raven/SanitizeDataProcessor.php");
require_once(dirname( __FILE__ ) . "/raven/Serializer.php");
require_once(dirname( __FILE__ ) . "/raven/Stacktrace.php");
require_once(dirname( __FILE__ ) . "/raven/Util.php");



$client = new Raven_Client('https://5b9554f556d54dc0b94f3ba5261c09b3:036021f6b3ca4222a9157119adae14ee@app.getsentry.com/12233');

$error_handler = new Raven_ErrorHandler($client);
$error_handler->registerExceptionHandler();
$error_handler->registerErrorHandler();
$error_handler->registerShutdownFunction();
?>