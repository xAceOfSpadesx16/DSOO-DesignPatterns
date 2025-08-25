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

    /**
     * Constructor de HtmlElement.
     */
    public function __construct(TagName $tagName, ?string $id = null, ?array $classes = null, ?array $styles = null, ?array $attributes = null, ?array $children = null, ?array $allowedChildren = null) {
        $this->tagName = $tagName;
        $this->id = $id;
        $this->classes = $classes;
        $this->styles = $styles;
        $this->attributes = $attributes;
        $this->allowedChildren = $allowedChildren ?? [];
        $this->appendChildren(...$children ?? []);
    }

    /**
     * Obtiene el elemento padre.
     */
    public function getParent(): ?NodeInterface {
        return $this->parent;
    }

    /**
     * Establece el elemento padre.
     */
    public function setParent(?NodeInterface $parent): static {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Obtiene los hijos.
     */
    public function getChildren(): array {
        return $this->children;
    }

    /**
     * Verifica si tiene hijos.
     */
    public function hasChildren(): bool {
        return !empty($this->children);
    }

    /**
     * Agrega hijos al elemento.
     */
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

    /**
     * Agrega un hijo al inicio.
     */
    public function prependChild(NodeInterface $child): static {
        $validChildren = $this->validateChildren($child);
        if (!$validChildren) {
            throw new \InvalidArgumentException("El hijo proporcionado no es válido.");
        }

        array_unshift($this->children, $child);
        $child->setParent($this);
        return $this;
    }

    /**
     * Inserta un hijo en una posición específica.
     */
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

    /**
     * Elimina un hijo.
     */
    public function removeChild(NodeInterface $child): static {
        $key = array_search($child, $this->children, true);
        if ($key !== false) {
            unset($this->children[$key]);
            $this->children = array_values($this->children); // Reindexacion
            $child->setParent(null);
        }
        return $this;
    }

    /**
     * Elimina todos los hijos.
     */
    public function clearChildren(): static {
        foreach ($this->children as $child) { $child->setParent(null); }
        $this->children = [];
        return $this;
    }

    /**
     * Verifica si el nodo dado es ancestro.
     */
    public function isAncestor(NodeInterface $node): bool {
        if ($this->parent === null) return false;
        if ($this->parent === $node) return true;
        return $this->parent->isAncestor($node);
    }

    /**
     * Verifica si el hijo es permitido.
     */
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

    /**
     * Obtiene el nombre de la etiqueta HTML.
     */
    public function getTagName(): string {
        return $this->tagName->value;
    }

    /**
     * Agrega una clase CSS.
     */
    public function addClass(string $class): static {
        $this->classes ??= [];
        if (!in_array($class, $this->classes, true)) {
            $this->classes[] = $class;
        }
        return $this;
    }

    /**
     * Elimina una clase CSS.
     */
    public function removeClass(string $class): static {
        if (!$this->classes) return $this;
        if (($key = array_search($class, $this->classes, true)) !== false) {
            unset($this->classes[$key]);
        }
        return $this;
    }

    /**
     * Obtiene las clases CSS.
     */
    public function getClasses(): ?array {
        return $this->classes;
    }

    /**
     * Establece un estilo en línea.
     */
    public function setInlineStyle(string $property, string $value): static {
        $this->styles ??= [];
        $this->styles[strtolower($property)] = strtolower($value);
        return $this;
    }

    /**
     * Elimina un estilo en línea.
     */
    public function removeInlineStyle(string $property): static {
        if (!$this->styles) return $this;
        unset($this->styles[strtolower($property)]);
        return $this;
    }

    /**
     * Obtiene los estilos en línea.
     */
    public function getInlineStyles(): ?array {
        return $this->styles;
    }

    /**
     * Establece un atributo HTML.
     */
    public function setAttribute(string $name, string $value): static {
        $this->attributes ??= [];
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Elimina un atributo HTML.
     */
    public function removeAttribute(string $name): static {
        if (!$this->attributes) return $this;
        unset($this->attributes[$name]);
        return $this;
    }

    /**
     * Obtiene los atributos HTML.
     */
    public function getAttributes(): ?array {
        return $this->attributes;
    }

    /**
     * Establece el ID del elemento.
     */
    public function setId(string $id): static {
        $this->id = $id;
        return $this;
    }

    /**
     * Obtiene el ID del elemento.
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * Elimina el ID del elemento.
     */
    public function removeId(): static {
        $this->id = null;
        return $this;
    }

    /**
     * Obtiene el siguiente hijo (para iteración).
     */
    public function next(): ?NodeInterface {
        return $this->children[$this->currentChildIndex++] ?? null;
    }

    /**
     * Verifica si hay más hijos (para iteración).
     */
    public function hasNext(): bool {
        return isset($this->children[$this->currentChildIndex]);
    }

    /**
     * Reinicia el índice de iteración de hijos.
     */
    public function rewind(): void {
        $this->currentChildIndex = 0;
    }
}

class TextElement extends BaseTextElement {
    private ?NodeInterface $parent = null;
    private string $text = "";
    private TagName $tagName= TagName::STRING;

    /**
     * Constructor de TextElement.
     */
    public function __construct(string $text, ?NodeInterface $parent = null) {
        $this->setText($text);
        $this->parent = $parent;
    }

    /**
     * Obtiene el elemento padre.
     */
    public function getParent(): ?NodeInterface {
        return $this->parent;
    }

    /**
     * Establece el elemento padre.
     */
    public function setParent(?NodeInterface $parent): static {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Obtiene los hijos (siempre vacío).
     */
    public function getChildren(): array {
        return [];
    }

    /**
     * Verifica si tiene hijos (siempre falso).
     */
    public function hasChildren(): bool {
        return false;
    }

    /**
     * Métodos para manipulación de hijos (no permitido).
     */
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

    /**
     * Verifica si el nodo dado es ancestro (siempre falso).
     */
    public function isAncestor(NodeInterface $node): bool {
        return false;
    }

    /**
     * Verifica si el hijo es permitido (siempre falso).
     */
    public function isAllowedChild(NodeInterface $child): bool {
        return false;
    }

    /**
     * Establece el texto.
     */
    public function setText(string $text): static {
        $this->text = htmlspecialchars($text);
        return $this;
    }

    /**
     * Obtiene el texto.
     */
    public function getText(): string {
        return htmlspecialchars_decode($this->text);
    }

    /**
     * Obtiene el nombre de la etiqueta (STRING).
     */
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