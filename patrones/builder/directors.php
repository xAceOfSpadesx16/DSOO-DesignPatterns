<?php
namespace Builder\Directors;

require_once 'interfaces.php';

use Builder\Interfaces\TableBuilderInterface;
use Builder\Interfaces\NodeHTMLElementInterface;

class TableDirector {
    private TableBuilderInterface $builder;

    /**
     * Constructor de TableDirector.
     * @param TableBuilderInterface $builder El builder de la tabla.
     */
    public function __construct(TableBuilderInterface $builder) {
        $this->builder = $builder;
    }

    /**
     * Crea una tabla simple con encabezados, filas y footer.
     * @param array|null $headers Encabezados de la tabla.
     * @param array|null $rows Filas de la tabla.
     * @param array|null $footers Footers de la tabla.
     * @return NodeHTMLElementInterface La tabla construida.
     */
    public function makeSimpleTable(?array $headers, ?array $rows, ?array $footers): NodeHTMLElementInterface {
        $this->builder->reset();
        $this->builder->setTableHeader($headers);
        $this->builder->setTableBody($rows);
        $this->builder->setTableFooter($footers);
        return $this->builder->build();
    }

    /**
     * Crea una tabla a partir de un dataset.
     * @param array $dataset Datos con 'headers', 'rows' y 'footers'.
     * @return NodeHTMLElementInterface La tabla construida.
     */
    public function makeFromDataset(array $dataset): NodeHTMLElementInterface {
        $headers = $dataset['headers'] ?? null;
        $rows = $dataset['rows'] ?? null;
        $footers = $dataset['footers'] ?? null;

        return $this->makeSimpleTable($headers, $rows, $footers);
    }
}