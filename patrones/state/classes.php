<?php

namespace State\Classes;

require_once 'interfaces.php';
require_once 'states.php';

use State\Interfaces\Stateful;
use State\Interfaces\StateInterface;
use State\States\DefaultState;



final class StatefulElement implements Stateful {
    private StateInterface $state;
    private string $text = '';

    /**
     * Constructor de StatefulElement.
     * @param StateInterface|null $state Estado inicial opcional.
     */
    public function __construct(?StateInterface $state = null) {
        $this->transitionTo($state ?? new DefaultState());
    }

    // ——— Stateful ———

    /**
     * Obtiene el estado actual.
     * @return StateInterface
     */
    public function getState(): StateInterface {
        return $this->state;
    }

    /**
     * Aplica un estado al elemento.
     * @param StateInterface $state
     * @return void
     */
    public function applyState(StateInterface $state): void {
        $this->state = $state;
        $this->text  = $state->getText();
    }

    /**
     * Realiza la transición a un nuevo estado.
     * @param StateInterface $state
     * @return void
     */
    public function transitionTo(StateInterface $state): void {
        $state->apply($this);
    }

    /**
     * Establece el texto del elemento.
     * @param string $text
     * @return void
     */
    public function setText(string $text): void { $this->text = $text; }

    /**
     * Obtiene el texto del elemento.
     * @return string
     */
    public function getText(): string { return $this->text; }

    /**
     * Renderiza el elemento como HTML.
     * @return string
     */
    public function renderHtml(): string {
        $name = strtoupper($this->state->name());
        $txt  = htmlspecialchars($this->text, ENT_QUOTES, 'UTF-8');
        return "<h1>Elemento con estado: ($name)</h1><br><p>$txt</p>";
    }
}