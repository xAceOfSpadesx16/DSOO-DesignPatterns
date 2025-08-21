<?php
namespace Builder\Interfaces;

require_once 'enums.php';
require_once 'elements.php';

use Builder\Enums\TagName;

// Element interfaces

interface TaggedInterface {
    public function getTagName(): string;
}

interface ClassListInterface {
    public function addClass(string $class): static;
    public function removeClass(string $class): static;
    public function getClasses(): ?array;
}

interface InlineStyleInterface {
    public function setInlineStyle(string $property, string $value): static;
    public function removeInlineStyle(string $property): static;
    public function getInlineStyles(): ?array;
}

interface AttributeInterface {
    public function setAttribute(string $name, string $value): static;
    public function removeAttribute(string $name): static;
    public function getAttributes(): ?array;
}

interface IdentifierInterface {
    public function setId(string $id): static;
    public function removeId(): static;
    public function getId(): ?string;
}

interface RawTextInterface {
    public function setText(string $text): static;
    public function getText(): ?string;
}

interface HTMLElementInterface extends
    ClassListInterface,
    InlineStyleInterface,
    AttributeInterface,
    IdentifierInterface {
}

interface RawTextElementInterface extends RawTextInterface {
}

// Builder interfaces

interface ElementBuilderInterface {
    public function createHTMLElement(TagName $tagName): HTMLElementInterface;
    public function createTextElement(string $text): RawTextElementInterface;
}

interface TableElementBuilderInterface extends ElementBuilderInterface {
    public function createTable(): HTMLElementInterface;
    public function createTableHeader(): HTMLElementInterface;
    public function createTableBody(): HTMLElementInterface;
    public function createTableFooter(): HTMLElementInterface;
    public function createTableRow(): HTMLElementInterface;
    public function createTableDataCell(): HTMLElementInterface;
    public function createTableHeaderCell(): HTMLElementInterface;

}


?>