<?php
require_once 'phamlp/haml/HamlParser.php';
$source_file = $argv[1];
$output_file = $argv[2];



$haml = new HamlParser(array('style'=>'nested', 'ugly'=>false));
$output = $haml->parse($source_file);
$fd = fopen($output_file,'w');
fwrite($fd,$output);
fclose($fd);
?>