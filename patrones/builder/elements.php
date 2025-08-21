<?php
namespace Builder\Elements;

require_once "interfaces.php";
require_once dirname(__DIR__) . "\composite\interfaces.php";

use Builder\Enums\TagName;

use Builder\Interfaces\HTMLElementInterface;
use Builder\Interfaces\RawTextElementInterface;
use Builder\Interfaces\TaggedInterface;
use Composite\Interfaces\NodeInterface;

abstract class BaseElement implements TaggedInterface, NodeInterface {
}

class HtmlElement extends BaseElement implements HTMLElementInterface {
    private TagName $tagName;
    private NodeInterface $parent;
    private ?array $children = null;
    private ?array $classes = null;
    private ?array $styles = null;
    private ?array $attributes = null;
    private ?string $id = null;

    public function __construct(TagName $tagName) {
        $this->tagName = $tagName;
    }

    public function getTagName(): string {
        return $this->tagName->value;
    }

    public function getParent(): NodeInterface {
        return $this->parent;
    }

    public function setParent(?NodeInterface $parent): static {
        $this->parent = $parent;
        return $this;
    }
    public function getChildren(): iterable {
        return $this->children ?? [];
    }

    public function hasChildren(): bool {
        return !empty($this->children);
    }

    public function appendChild(NodeInterface $child): static {
        $this->children ??= [];
        $this->children[] = $child;
        $child->setParent($this);
        return $this;
    }

    public function prependChild(NodeInterface $child): static {
        $this->children ??= [];
        array_unshift($this->children, $child);
        $child->setParent($this);
        return $this;
    }

    public function insertChildAt(int $index, NodeInterface $child): static {
        if ($this === $child) {
            throw new \InvalidArgumentException("El nodo no puede insertarse a sí mismo.");
        }
        //implementar isAncestor?
        if ($this->parent === $child) {
            throw new \InvalidArgumentException("El nodo padre no puede insertarse como hijo.");
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

    public function clearChildren(): static{
        $this->children = null;
        return $this;
    }

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
class TextElement extends BaseElement implements RawTextElementInterface {
    private ?string $text = null;
    private TagName $tagName;
    private ?NodeInterface $parent = null;

    public function __construct() {
        $this->tagName = TagName::STRING;
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

    public function getChildren(): ?array {
       return null;
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
    public function __construct() {
        parent::__construct(tagName: TagName::TABLE);
    }
}

class TableHeaderElement extends HtmlElement {
    public function __construct() {
        parent::__construct(tagName: TagName::THEAD);
    }
}

class TableBodyElement extends HtmlElement {
    public function __construct() {
        parent::__construct(tagName: TagName::TBODY);
    }
}

class TableFooterElement extends HtmlElement {
    public function __construct() {
        parent::__construct(tagName: TagName::TFOOT);
    }
}

class TableRowElement extends HtmlElement {
    public function __construct() {
        parent::__construct(tagName: TagName::TR);
    }

}

class TableDataCellElement extends HtmlElement {
    public function __construct() {
        parent::__construct(tagName: TagName::TD);
    }
}

class TableHeaderCellElement extends HtmlElement {
    public function __construct() {
        parent::__construct(tagName: TagName::TH);
    }

}
