<?php
namespace Iterators\Interfaces;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "composite" . DIRECTORY_SEPARATOR . "interfaces.php";
use Composite\Interfaces\NodeInterface;

interface IterableNodeInterface extends NodeInterface{
    public function hasNext(): bool;
    public function next(): ?NodeInterface;

    public function rewind(): void;
}
