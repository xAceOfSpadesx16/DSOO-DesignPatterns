<?php

class HTMLCode {
    static string $htmlTag;
    public string $content;

    public function __construct(string $content) {
        $this->content = $content;
    }

    public static function buildMainTag(bool $initial=true): string {
        return "<" . ($initial ? "" : "/") . static::$htmlTag . ">";
    }

    public function getContent(): string {
        return $this->content;
    }
}

interface HTMLTableElement {
    public function buildHTMLCode(): string;
}

class HTMLTableDataCell extends HTMLCode implements HTMLTableElement {
    static string $htmlTag = "td";

    public function __construct(string $content) {
        parent::__construct($content);
    }

    public function buildHTMLCode(): string {
        $code = $this->buildMainTag();
        $code .= parent::getContent();
        $code .= $this->buildMainTag(false);
        return $code;
    }
}

class HTMLTableRow extends HTMLCode implements HTMLTableElement {
    static string $htmlTag = "tr";
    public function buildHTMLCode(): string {
        return "";
    }
}

class HTMLTableHeader extends HTMLCode implements HTMLTableElement {
    static string $htmlTag = "thead";
    public function buildHTMLCode(): string {
        return "";
    }
}

class HTMLTableFooter extends HTMLCode implements HTMLTableElement {
    static string $htmlTag = "tfoot";
    public function buildHTMLCode(): string {
        return "";
    }
}

class HTMLTable extends HTMLCode {
    static string $htmlTag = "table";
    private array $rows;
    private array $headers;
    private array $footers;

    public function __construct() {
        $this->rows = [];
        $this->headers = [];
        $this->footers = [];
    }

    public function addHeader(HTMLTableHeader $header): void {
        $this->headers[] = $header;
    }

    public function addRow(HTMLTableRow $row): void {
        $this->rows[] = $row;
    }

    public function addFooter(HTMLTableFooter $footer): void {
        $this->footers[] = $footer;
    }

    public function buildHTMLCode(): string {
        $html = "<table>";
        foreach ($this->headers as $header) {
            $html .= $header->buildHTMLCode();
        }
        foreach ($this->rows as $row) {
            $html .= $row->buildHTMLCode();
        }
        foreach ($this->footers as $footer) {
            $html .= $footer->buildHTMLCode();
        }
        $html .= "</table>";
        return $html;
    }
}

class HTMLTableBuilder {
    private HTMLTable $table;

    public function __construct() {
        $this->resetTable();
    }

    private function resetTable(): void {
        $this->table = new HTMLTable();
    }

    public function getTable(): HTMLTable {
        return $this->table;
    }

}




?>