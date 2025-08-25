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

    public function __construct(?StateInterface $state = null) {
        $this->transitionTo($state ?? new DefaultState());
    }

    // ——— Stateful ———
    public function getState(): StateInterface {
        return $this->state;
    }

    public function applyState(StateInterface $state): void {
        $this->state = $state;
        $this->text  = $state->getText();
    }

    public function transitionTo(StateInterface $state): void {
        $state->apply($this);
    }

    public function setText(string $text): void { $this->text = $text; }
    public function getText(): string { return $this->text; }

    public function renderHtml(): string {
        $name = strtoupper($this->state->name());
        $txt  = htmlspecialchars($this->text, ENT_QUOTES, 'UTF-8');
        return "<h1>Elemento con estado: ($name)</h1><br><p>$txt</p>";
    }
}