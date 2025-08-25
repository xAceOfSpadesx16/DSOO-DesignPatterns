<?php
namespace Builder\Interfaces;

require_once 'enums.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "composite" . DIRECTORY_SEPARATOR . "interfaces.php";
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "iterator" . DIRECTORY_SEPARATOR . "interfaces.php";

use Composite\Interfaces\NodeInterface;
use Iterators\Interfaces\IterableNodeInterface;



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


interface HTMLElementInterface extends TaggedInterface, ClassListInterface, InlineStyleInterface, AttributeInterface, IdentifierInterface {}

interface RawTextElementInterface extends TaggedInterface {
    public function setText(string $text): static;
    public function getText(): string;
}

interface NodeHTMLElementInterface extends HTMLElementInterface, IterableNodeInterface {}

interface RawTextNodeElementInterface extends RawTextElementInterface, NodeInterface {}



// Builder interfaces

interface BuilderInterface{
    public function build(): HTMLElementInterface;
    public function reset(): static;
}

interface TableBuilderInterface extends BuilderInterface {
    public function setTableHeader(?array $headers): static;
    public function setTableBody(?array $rows): static;
    public function setTableFooter(?array $footers): static;

}



// Render interfaces

interface RendererInterface {
    public function render(NodeInterface $element): string;
    public function renderElement(NodeHTMLElementInterface $element): string;
    public function renderTextElement(RawTextNodeElementInterface $element): string;
    
    // HTML attrs
    public function renderChildren(NodeHTMLElementInterface $element): string;
    public function renderId(NodeHTMLElementInterface $element): string;
    public function renderClasses(NodeHTMLElementInterface $element): string;

    public function renderStyles(NodeHTMLElementInterface $element): string;

    public function renderAttributes(NodeHTMLElementInterface $element): string;

}
