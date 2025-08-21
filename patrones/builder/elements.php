<?php
namespace Builder\Elements;

require_once __DIR__ . DIRECTORY_SEPARATOR . "interfaces.php";
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "composite" . DIRECTORY_SEPARATOR . "interfaces.php";

use Builder\Enums\TagName;

use Builder\Interfaces\HTMLElementInterface;
use Builder\Interfaces\RawTextElementInterface;
use Builder\Interfaces\TaggedInterface;
use Composite\Interfaces\NodeInterface;

class NodeElement implements NodeInterface{
    private ?NodeInterface $parent = null;
    private array $children = [];

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
        $this->children = array_merge($this->children, $children);
        foreach ($children as $child) {
            $child->setParent($this);
        }
        return $this;
    }

    public function prependChild(NodeInterface $child): static {
        $this->children ??= [];
        array_unshift($this->children, $child);
        $child->setParent($this);
        return $this;
    }

    public function insertChildAt(int $index, NodeInterface $child): static {
        if ($this->isAncestor($child)) {
            throw new \InvalidArgumentException("El nodo hijo no puede ser un ancestro.");
        }

        $this->children ??= [];
        array_splice($this->children, $index, 0, [$child]);
        $child->setParent($this);
        return $this;
    }

    public function removeChild(NodeInterface $child): static {
        if ($this->children) {
            $this->children = array_filter($this->children, fn($c) => $c !== $child);
            $child->setParent(null);
        }
        return $this;
    }

    public function clearChildren(): static {
        $this->children = [];
        return $this;
    }

    public function isAncestor(NodeInterface $node): bool {
        if ($this === $node) {
            return true;
        }
        $parent = $this->parent;
        while ($parent) {
            if ($parent === $node) {
                return true;
            }
            $parent = $parent->getParent();
        }
        return false;
    }
}

class NoChildNode implements NodeInterface {
    private ?NodeInterface $parent = null;

    public function __construct(?NodeInterface $parent = null) {
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

}

abstract class BaseElement implements TaggedInterface {
}

abstract class BaseHTMLElement extends NodeElement  implements HTMLElementInterface {}

abstract class BaseTextElement extends NoChildNode implements RawTextElementInterface {}

class HtmlElement extends BaseHTMLElement {
    private TagName $tagName;
    private ?array $classes = null;
    private ?array $styles = null;
    private ?array $attributes = null;
    private ?string $id = null;

    public function __construct(TagName $tagName, ?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        $this->tagName = $tagName;
        $this->id = $id;
        $this->classes = $classes;
        $this->styles = $styles;
        $this->attributes = $attributes;
        if ($children) {
            $this->appendChildren(...$children);
        }
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

}


// String Element
class TextElement extends BaseTextElement {
    private ?string $text = null;
    private TagName $tagName= TagName::STRING;
    private ?NodeInterface $parent = null;

    public function __construct(string $text) {
        $this->setText($text);
    }

    public function setText(string $text): static {
        $this->text = htmlspecialchars($text);
        return $this;
    }

    public function getText(): ?string {
        return htmlspecialchars_decode($this->text);
    }

    public function getTagName(): string {
        return $this->tagName->value;
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

    public function appendChild(NodeInterface $child): static {
        throw new \LogicException("Este elemento no puede tener hijos.");
    }

    public function prependChild(NodeInterface $child): static {
        throw new \LogicException("Este elemento no puede tener hijos.");
    }

    public function insertChildAt(int $index, NodeInterface $child): static {
        throw new \LogicException("Este elemento no puede tener hijos.");
    }

    public function removeChild(NodeInterface $child): static {
        throw new \LogicException("Este elemento no puede tener hijos.");
    }

    public function clearChildren(): static {
        throw new \LogicException("Este elemento no puede tener hijos.");
    }
}


// Table Elements

class TableElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        parent::__construct(tagName: TagName::TABLE, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children);
    }
}

class TableHeaderElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        parent::__construct(tagName: TagName::THEAD, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children);
    }
}

class TableBodyElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        parent::__construct(tagName: TagName::TBODY, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children);
    }
}

class TableFooterElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        parent::__construct(tagName: TagName::TFOOT, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children);
    }
}

class TableRowElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        parent::__construct(tagName: TagName::TR, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children);
    }

}

class TableDataCellElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        parent::__construct(tagName: TagName::TD, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children);
    }
}

class TableHeaderCellElement extends HtmlElement {
    public function __construct(?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null) {
        parent::__construct(tagName: TagName::TH, id: $id, classes: $classes, styles: $styles, attributes: $attributes, children: $children);
    }

}
