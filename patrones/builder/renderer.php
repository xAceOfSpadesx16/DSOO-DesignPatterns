<?php
namespace Builder\Renderers;

require_once 'interfaces.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "composite" . DIRECTORY_SEPARATOR . "interfaces.php";
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "iterator" . DIRECTORY_SEPARATOR . "interfaces.php";

use Composite\Interfaces\NodeInterface;
use Iterators\Interfaces\IterableNodeInterface;
use Builder\Interfaces\NodeHTMLElementInterface;
use Builder\Interfaces\RawTextNodeElementInterface;
use Builder\Interfaces\RendererInterface;

// APLICACION DEL PATRON ITERATOR EN METODO RENDERCHILDREN
class HtmlRenderer implements RendererInterface {
    public function render(NodeInterface $element): string {

        if ($element instanceof RawTextNodeElementInterface) {
            return $this->renderTextElement($element);

        } elseif ($element instanceof NodeHTMLElementInterface) {
            return $this->renderElement($element);
        }
        throw new \InvalidArgumentException("Elemento no soportado.");
    }

    public function renderElement(NodeHTMLElementInterface $element): string {
        $tagName = $element->getTagName();

        $parts = [
            $this->renderId($element),
            $this->renderClasses($element),
            $this->renderAttributes($element),
            $this->renderStyles($element),
        ];
        $parts = array_values(array_filter($parts, fn($p) => $p !== '' && $p !== null));
        $attrs = empty($parts) ? '' : ' ' . implode(' ', $parts);

        // Cambiar en caso de Void elements. (no implementados, ejemplo <br>, <img>, etc)
        $openingTag = "<" . $tagName . $attrs . ">";
        $closingTag = "</$tagName>";

        return $openingTag . $this->renderChildren($element) . $closingTag;
    }

    public function renderTextElement(RawTextNodeElementInterface $element): string {
        return htmlspecialchars($element->getText());
    }

    public function renderChildren(NodeHTMLElementInterface $element): string {
        if (!($element instanceof IterableNodeInterface)) {
            return '';
        }

        // Aplicacion de Patron Iterator

        $element->rewind();

        $html = '';
        while ($element->hasNext()){
            $html .= $this->render($element->next());
        }
        return $html;
    }

    public function renderId(NodeHTMLElementInterface $element): string {
        $id = $element->getId();
        return $id ? 'id="' . htmlspecialchars($id) . '"' : '';
    }

    public function renderClasses(NodeHTMLElementInterface $element): string {
        $classes = $element->getClasses();
        return $classes ? 'class="' . htmlspecialchars(implode(' ', $classes)) . '"' : '';
    }

    public function renderStyles(NodeHTMLElementInterface $element): string {
        $styles = $element->getInlineStyles();
        if (empty($styles)) {
            return '';
        }
        $styleString = '';
        foreach ($styles as $property => $value) {
            $styleString .= htmlspecialchars($property) . ': ' . htmlspecialchars($value) . '; ';
        }
        return 'style="' . rtrim($styleString) . '"';
    }

    public function renderAttributes(NodeHTMLElementInterface $element): string {
        $attributes = $element->getAttributes();
        if (empty($attributes)) {
            return '';
        }
        $parts = [];
        foreach ($attributes as $name => $value) {
            $n = trim((string)$name);
            if ($n === '') continue;
            if ($value === null) {
                $parts[] = htmlspecialchars($n);
            } else {
                $parts[] = htmlspecialchars($n) . '="' . htmlspecialchars((string)$value) . '"';
            }
        }
        return empty($parts) ? '' : implode(' ', $parts);
    }
}
