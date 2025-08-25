<?php
namespace Builder\Builders;

require_once 'interfaces.php';
require_once 'elements.php';

use Builder\Interfaces\TableBuilderInterface;
use Builder\Elements\TableElement;
use Builder\Elements\TableHeaderElement;
use Builder\Elements\TableBodyElement;
use Builder\Elements\TableFooterElement;
use Builder\Elements\TableRowElement;
use Builder\Elements\TableDataCellElement;
use Builder\Elements\TableHeaderCellElement;
use Builder\Elements\TextElement;

final class TableBuilder implements TableBuilderInterface {
    private ?TableElement $table = null;

    public function __construct() {
        $this->reset();
    }

    public function reset(): static {
        $this->table = new TableElement(classes: ["table", "table-bordered"], styles: ["border" => "1px solid black"], attributes: ["data-algo" => "valor", "title" => "Tabla de ejemplo"]);
        return $this;
    }


    public function setTableHeader(?array $headers): static {
        if (empty($headers)) return $this;

        $thead = new TableHeaderElement(styles: ["background" => "lightblue"]);
        $tr = new TableRowElement();

        foreach ($headers as $content) {
            $th = new TableHeaderCellElement(styles: ["border" => "1px solid black"]);
            $th->appendChildren(new TextElement((string)$content));

            $tr->appendChildren($th);
        }

        $thead->appendChildren($tr);

        $this->table->appendChildren($thead);

        return $this;
    }

    public function setTableBody(?array $rows): static {
        if (empty($rows)) return $this;

        $tbody = new TableBodyElement();

        foreach ($rows as $row) {
            $tr = new TableRowElement();
            foreach ($row as $cellContent) {
                $td = new TableDataCellElement(styles: ["border" => "1px solid black"]);
                $td->appendChildren(new TextElement((string)$cellContent));

                $tr->appendChildren($td);
            }

            $tbody->appendChildren($tr);
        }

        $this->table->appendChildren($tbody);
        return $this;
    }

    public function setTableFooter(?array $footers): static {
        if (empty($footers)) return $this;

        $tfoot = new TableFooterElement();

        $tr = new TableRowElement();

        foreach ($footers as $content) {
            $td = new TableDataCellElement();
            $td->appendChildren(new TextElement((string)$content));

            $tr->appendChildren($td);
        }

        $tfoot->appendChildren($tr);

        $this->table->appendChildren($tfoot);
        return $this;
    }

    public function build(): TableElement {
        $atLeastOneBody = !empty(array_filter(
            $this->table->getChildren(),
            fn($c) => $c instanceof TableBodyElement
        ));

        if (!$atLeastOneBody) {
            throw new \Exception("La tabla debe tener al menos un tbody.");
        }
        return $this->table;
    
    }
}