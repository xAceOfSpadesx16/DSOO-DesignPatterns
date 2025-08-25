<?php
namespace Builder\Elements;


require_once __DIR__ . DIRECTORY_SEPARATOR . "interfaces.php";
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "composite" . DIRECTORY_SEPARATOR . "interfaces.php";
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "state" . DIRECTORY_SEPARATOR . "interfaces.php";
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "state" . DIRECTORY_SEPARATOR . "states.php";

use Builder\Enums\TagName;

use Builder\Interfaces\NodeHTMLElementInterface;
use Builder\Interfaces\RawTextNodeElementInterface;
use Composite\Interfaces\NodeInterface;

abstract class BaseHTMLElement implements NodeHTMLElementInterface {}

abstract class BaseTextElement implements RawTextNodeElementInterface {}

class HtmlElement extends BaseHTMLElement {
    private TagName $tagName;
    private ?array $classes = null;
    private ?array $styles = null;
    private ?array $attributes = null;
    private ?string $id = null;
    private ?NodeInterface $parent = null;
    private array $children = [];
    private array $allowedChildren = [];

    private int $currentChildIndex = 0;

    public function __construct(TagName $tagName, ?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null, ?array $allowedChildren = null) {
        $this->tagName = $tagName;
        $this->id = $id;
        $this->classes = $classes;
        $this->styles = $styles;
        $this->attributes = $attributes;
        $this->allowedChildren = $allowedChildren ?? [];
        $this->appendChildren(...$children ?? []);
    }

    public function getParent(): ?NodeInterface {
        return $this->parent;
    }

    public function setParent(?NodeInterface $parent): static {
        $this->parent = $parent;
        return $this;
    }

    public function getChildren(): array {
        return $this->children;
    }

    public function hasChildren(): bool {
        return !empty($this->children);
    }

    public function appendChildren(NodeInterface ...$children): static {
        $validChildren = $this->validateChildren(...$children);
        if (!$validChildren) {
            throw new \InvalidArgumentException("Los hijos proporcionados no son válidos.");
        }

        $this->children = array_merge($this->children, $children);
        foreach ($children as $child) {
            $child->setParent($this);
        }
        return $this;
    }

    public function prependChild(NodeInterface $child): static {
        $validChildren = $this->validateChildren($child);
        if (!$validChildren) {
            throw new \InvalidArgumentException("El hijo proporcionado no es válido.");
        }

        array_unshift($this->children, $child);
        $child->setParent($this);
        return $this;
    }

    public function insertChildAt(int $index, NodeInterface $child): static {
        $validChildren = $this->validateChildren($child);
        if (!$validChildren) {
            throw new \InvalidArgumentException("El hijo proporcionado no es válido.");
        }

        $index = max(0, min($index, count($this->children))); // Mantener dentro del limite.

        array_splice($this->children, $index, 0, [$child]);
        $child->setParent($this);
        return $this;
    }

    public function removeChild(NodeInterface $child): static {
        $key = array_search($child, $this->children, true);
        if ($key !== false) {
            unset($this->children[$key]);
            $this->children = array_values($this->children); // Reindexacion
            $child->setParent(null);
        }
        return $this;
    }

    public function clearChildren(): static {
        foreach ($this->children as $child) { $child->setParent(null); }
        $this->children = [];
        return $this;
    }

    public function isAncestor(NodeInterface $node): bool {
        if ($this->parent === null) return false;
        if ($this->parent === $node) return true;
        return $this->parent->isAncestor($node);
    }

    public function isAllowedChild(NodeInterface $child): bool {
        return empty($this->allowedChildren) || in_array($child::class, $this->allowedChildren, true);
    }

    // ValidationMethod
    private function validateChildren(NodeInterface ...$children): bool {
        foreach ($children as $child) {
            if ($child === $this) return false;

            if (!$this->isAllowedChild($child)) return false;

            if ($child->isAncestor($this)) return false;
        }
        return true;
    }

    // TaggedInterface Method
    public function getTagName(): string {
        return $this->tagName->value;
    }

    // HTMLInterface Methods
    public function addClass(string $class): static {
        $this->classes ??= [];
        if (!in_array($class, $this->classes, true)) {
            $this->classes[] = $class;
        }
        return $this;
    }

    public function removeClass(string $class): static {
        if (!$this->classes) return $this;
        if (($key = array_search($class, $this->classes, true)) !== false) {
            unset($this->classes[$key]);
        }
        return $this;
    }

    public function getClasses(): ?array {
        return $this->classes;
    }

    public function setInlineStyle(string $property, string $value): static {
        $this->styles ??= [];
        $this->styles[strtolower($property)] = strtolower($value);
        return $this;
    }

    public function removeInlineStyle(string $property): static {
        if (!$this->styles) return $this;
        unset($this->styles[strtolower($property)]);
        return $this;
    }

    public function getInlineStyles(): ?array {
        return $this->styles;
    }

    public function setAttribute(string $name, string $value): static {
        $this->attributes ??= [];
        $this->attributes[$name] = $value;
        return $this;
    }

    public function removeAttribute(string $name): static {
        if (!$this->attributes) return $this;
        unset($this->attributes[$name]);
        return $this;
    }

    public function getAttributes(): ?array {
        return $this->attributes;
    }

    public function setId(string $id): static {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function removeId(): static {
        $this->id = null;
        return $this;
    }


    public function next(): ?NodeInterface {
        return $this->children[$this->currentChildIndex++] ?? null;
    }

    public function hasNext(): bool {
        return isset($this->children[$this->currentChildIndex]);
    }

    public function rewind(): void {
        $this->currentChildIndex = 0;
    }
}

class TextElement extends BaseTextElement {
    private ?NodeInterface $parent = null;
    private string $text = "";
    private TagName $tagName= TagName::STRING;

    public function __construct(string $text, ?NodeInterface $parent = null) {
        $this->setText($text);
        $this->parent = $parent;
    }

    public function getParent(): ?NodeInterface {
        return $this->parent;
    }

    public function setParent(?NodeInterface $parent): static {
        $this->parent = $parent;
        return $this;
    }

    public function getChildren(): array {
        return [];
    }

    public function hasChildren(): bool {
        return false;
    }

    public function appendChildren(NodeInterface ...$children): static {
        throw new \BadMethodCallException("No se pueden agregar hijos a este nodo.");
    }

    public function prependChild(NodeInterface $child): static {
        throw new \BadMethodCallException("No se pueden agregar hijos a este nodo.");
    }

    public function insertChildAt(int $index, NodeInterface $child): static {
        throw new \BadMethodCallException("No se pueden agregar hijos a este nodo.");
    }

    public function removeChild(NodeInterface $child): static {
        throw new \BadMethodCallException("No se pueden eliminar hijos de este nodo.");
    }

    public function clearChildren(): static {
        throw new \BadMethodCallException("No se pueden eliminar hijos de este nodo.");
    }

    public function isAncestor(NodeInterface $node): bool {
        return false;
    }

    public function isAllowedChild(NodeInterface $child): bool {
        return false;
    }
    public function setText(string $text): static {
        $this->text = htmlspecialchars($text);
        return $this;
    }

    public function getText(): string {
        return htmlspecialchars_decode($this->text);
    }

    public function getTagName(): string {
        return $this->tagName->value;
    }
}


// Table Elements

class TableElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        $allowedChildren = [
            TableHeaderElement::class,
            TableBodyElement::class,
            TableFooterElement::class,
        ];
        parent::__construct(tagName: TagName::TABLE, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children, allowedChildren: $allowedChildren);
    }
}

class TableHeaderElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        $allowedChildren = [
            TableRowElement::class
        ];
        parent::__construct(tagName: TagName::THEAD, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children, allowedChildren: $allowedChildren);
    }
}

class TableBodyElement extends HtmlElement {

    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        $allowedChildren = [
            TableRowElement::class
        ];
        parent::__construct(tagName: TagName::TBODY, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children, allowedChildren: $allowedChildren);
    }
}

class TableFooterElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        $allowedChildren = [
            TableRowElement::class
        ];
        parent::__construct(tagName: TagName::TFOOT, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children, allowedChildren: $allowedChildren);
    }
}

class TableRowElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        $allowedChildren = [
            TableDataCellElement::class,
            TableHeaderCellElement::class
        ];
        parent::__construct(tagName: TagName::TR, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children, allowedChildren: $allowedChildren);
    }

}

class TableDataCellElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        $allowedChildren = [];
        parent::__construct(tagName: TagName::TD, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children, allowedChildren: $allowedChildren);
    }
}

class TableHeaderCellElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        $allowedChildren = [];
        parent::__construct(tagName: TagName::TH, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children, allowedChildren: $allowedChildren);
    }

}