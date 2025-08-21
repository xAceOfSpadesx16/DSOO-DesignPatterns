<?php
namespace Composite\Interfaces;

interface NodeInterface {

    public function getParent(): ?NodeInterface;

    public function setParent(?NodeInterface $parent): static;

    public function getChildren(): ?iterable;

    public function hasChildren(): bool;

    public function appendChild(NodeInterface $child): static;

    public function prependChild(NodeInterface $child): static;

    public function insertChildAt(int $index, NodeInterface $child): static;

    public function removeChild(NodeInterface $child): static;

    public function clearChildren(): static;
}