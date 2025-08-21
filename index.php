<?php

use Builder\Interfaces\RawTextElementInterface;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once 'patrones/builder/elements.php';

use Builder\Elements\TableElement;
use Builder\Elements\TableRowElement;
use Builder\Elements\TableDataCellElement;
use Builder\Elements\TextElement;

$table = new TableElement(
    id: 'myTable', 
    classes: ['table', 'table-striped'], 
    styles: ['width' => '100%'], 
    children: [
        new TableRowElement(
            id: 'row1', 
            classes: ['table-row'], 
            styles: ['background-color' => '#f9f9f9'], 
            children: [
                new TableDataCellElement(
                    id: 'cell1', 
                    classes: ['table-cell'], 
                    styles: ['padding' => '8px'], 
                    children: [
                        new TextElement('Cell 1')
                    ]
                ),
                new TableDataCellElement(
                    id: 'cell2', 
                    classes: ['table-cell'], 
                    styles: ['padding' => '8px'], 
                    children: [
                        new TextElement('Cell 2')
                    ]
                )
            ]
        ),
        new TableRowElement(
            id: 'row2', 
            classes: ['table-row'], 
            styles: ['background-color' => '#f2f2f2'], 
            children: [
                new TableDataCellElement(
                    id: 'cell3', 
                    classes: ['table-cell'], 
                    styles: ['padding' => '8px'], 
                    children: [
                        new TextElement('Cell 3')
                    ]
                )
            ]
        )
    ]
);



echo($table->getTagName());
echo "<br>";

function displayChildren(iterable $children, int $level = 1): void {
    foreach ($children as $child) {
        echo str_repeat("-", $level * 4). $child->getTagName(). "<br>";
        if($child->hasChildren()) {
            displayChildren($child->getChildren(), $level + 1);
        }
        if (!$child instanceof RawTextElementInterface) {
            echo str_repeat("-", $level * 4). "/". $child->getTagName(). "<br>";
        }
    }
}

displayChildren($table->getChildren());