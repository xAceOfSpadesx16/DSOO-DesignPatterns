<?php
namespace Composite\Interfaces;

/**
 * Contrato para nodos en estructuras de árbol, permitiendo manipulación de padres e hijos.
 */
interface NodeInterface {

    public function getParent(): ?NodeInterface;

    public function setParent(?NodeInterface $parent): static;

    public function getChildren(): iterable;

    public function hasChildren(): bool;

    public function appendChildren(NodeInterface ...$children): static;

    public function prependChild(NodeInterface $child): static;

    public function insertChildAt(int $index, NodeInterface $child): static;

    public function removeChild(NodeInterface $child): static;

    public function clearChildren(): static;

    public function isAncestor(NodeInterface $node): bool;

    public function isAllowedChild(NodeInterface $child): bool;
}