<?php
namespace Iterators\Interfaces;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "composite" . DIRECTORY_SEPARATOR . "interfaces.php";
use Composite\Interfaces\NodeInterface;

/**
 * Contrato para nodos que pueden ser recorridos mediante un iterador personalizado.
 */
interface IterableNodeInterface extends NodeInterface{
    public function hasNext(): bool;
    public function next(): ?NodeInterface;

    public function rewind(): void;
}
