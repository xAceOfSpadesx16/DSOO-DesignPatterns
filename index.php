<?php
require_once 'patrones/builder/elements.php';

use Builder\Elements\TableElement;

$table = new TableElement()->addClass("my-table");

var_dump($table->getTagName());
var_dump($table->getClasses());
var_dump($table->getAttributes());