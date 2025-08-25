<?php
namespace Builder\Interfaces;

require_once 'enums.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "composite" . DIRECTORY_SEPARATOR . "interfaces.php";
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "iterator" . DIRECTORY_SEPARATOR . "interfaces.php";

use Composite\Interfaces\NodeInterface;
use Iterators\Interfaces\IterableNodeInterface;



// Element interfaces

/**
 * Contrato para obtener el nombre de la etiqueta HTML.
 */
interface TaggedInterface {
    public function getTagName(): string;
}

/**
 * Contrato para manipular la lista de clases CSS.
 */
interface ClassListInterface {
    public function addClass(string $class): static;
    public function removeClass(string $class): static;
    public function getClasses(): ?array;
}

/**
 * Contrato para manipular estilos en línea.
 */
interface InlineStyleInterface {
    public function setInlineStyle(string $property, string $value): static;
    public function removeInlineStyle(string $property): static;
    public function getInlineStyles(): ?array;
}

/**
 * Contrato para manipular atributos HTML.
 */
interface AttributeInterface {
    public function setAttribute(string $name, string $value): static;
    public function removeAttribute(string $name): static;
    public function getAttributes(): ?array;
}

/**
 * Contrato para manipular el identificador (id) de un elemento.
 */
interface IdentifierInterface {
    public function setId(string $id): static;
    public function removeId(): static;
    public function getId(): ?string;
}

/**
 * Contrato general para elementos HTML completos.
 */
interface HTMLElementInterface extends TaggedInterface, ClassListInterface, InlineStyleInterface, AttributeInterface, IdentifierInterface {}

/**
 * Contrato para elementos de texto sin formato.
 */
interface RawTextElementInterface extends TaggedInterface {
    public function setText(string $text): static;
    public function getText(): string;
}

/**
 * Contrato para nodos de elementos HTML que pueden ser iterados.
 */
interface NodeHTMLElementInterface extends HTMLElementInterface, IterableNodeInterface {}

/**
 * Contrato para nodos de texto sin formato.
 */
interface RawTextNodeElementInterface extends RawTextElementInterface, NodeInterface {}



// Builder interfaces

/**
 * Contrato base para builders de elementos HTML.
 */
interface BuilderInterface{
    public function build(): HTMLElementInterface;
    public function reset(): static;
}

/**
 * Contrato específico para builders de tablas HTML.
 */
interface TableBuilderInterface extends BuilderInterface {
    public function setTableHeader(?array $headers): static;
    public function setTableBody(?array $rows): static;
    public function setTableFooter(?array $footers): static;

}



// Render interfaces

/**
 * Contrato para renderizadores de nodos HTML y texto.
 */
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
