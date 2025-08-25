<?php

require_once 'classes.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'proxy' . DIRECTORY_SEPARATOR . 'classes.php';

use State\Classes\StatefulElement;
use Proxy\StateProxy;

$statefulElement = new StatefulElement();
$stateProxy = new StateProxy($statefulElement);

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

echo $stateProxy->renderHtml();