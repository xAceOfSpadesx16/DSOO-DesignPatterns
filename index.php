<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once 'patrones/builder/elements.php';

use Builder\Elements\TableElement;
use Builder\Elements\TableRowElement;
use Builder\Elements\TableDataCellElement;
use Builder\Elements\TextElement;

$table = (new TableElement())
    ->addClass('my-table')
    ->appendChild(
        child: (new TableRowElement())
            ->addClass('my-row')
            ->appendChild(
                child: (new TableDataCellElement())
                ->addClass(class: 'my-cell')
                ->appendChild(child: new TextElement('Cell 1'))
            )
    )
    ->appendChild(child: (new TableDataCellElement())
        ->addClass(class: 'my-cell')
        ->appendChild(child: new TextElement('Cell 2'))
    )
    ->appendChild(child: (new TableRowElement())
        ->addClass(class: 'my-row')
        ->appendChild(
            child: (new TableDataCellElement())
            ->addClass(class: 'my-cell')
            ->appendChild(child: new TextElement('Cell 3'))
        )
    );


echo($table->getTagName());
echo "<br>";

function displayChildren(iterable $children): void {
    foreach ($children as $child) {
        echo $child->getTagName(). "<br>";
        if($child->hasChildren()) {
            displayChildren($child->getChildren());
        }
    }
}

displayChildren($table->getChildren());