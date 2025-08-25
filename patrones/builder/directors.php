<?php
namespace Builder\Directors;

require_once 'interfaces.php';

use Builder\Interfaces\TableBuilderInterface;
use Builder\Interfaces\HTMLElementInterface;

class TableDirector {
    private TableBuilderInterface $builder;

    public function __construct(TableBuilderInterface $builder) {
        $this->builder = $builder;
    }

    public function makeSimpleTable(?array $headers, ?array $rows, ?array $footers): HTMLElementInterface {
        $this->builder->reset();
        $this->builder->setTableHeader($headers);
        $this->builder->setTableBody($rows);
        $this->builder->setTableFooter($footers);
        return $this->builder->build();
    }

    public function makeFromDataset(array $dataset): HTMLElementInterface {
        $headers = $dataset['headers'] ?? null;
        $rows = $dataset['rows'] ?? null;
        $footers = $dataset['footers'] ?? null;

        return $this->makeSimpleTable($headers, $rows, $footers);
    }
}