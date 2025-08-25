<?php

namespace State\States;

require_once "interfaces.php";

use State\Interfaces\StateInterface;
use State\Interfaces\Stateful;

abstract class BaseState implements StateInterface {
    /**
     * Aplica el estado al objeto dado.
     * @param Stateful $obj
     * @return void
     */
    public function apply(Stateful $obj): void { 
        $obj->applyState($this);
    }
}


final class DefaultState extends BaseState {
    /**
     * Nombre del estado.
     * @return string
     */
    public function name(): string { return 'default'; }

    /**
     * Texto descriptivo del estado.
     * @return string
     */
    public function getText(): string {
        return 'Estado por defecto (navegación normal).';
    }
}

final class GetState extends BaseState {
    /**
     * Nombre del estado.
     * @return string
     */
    public function name(): string { return 'get'; }

    /**
     * Texto descriptivo del estado.
     * @return string
     */
    public function getText(): string {
        return 'Estado GET vía fetch/AJAX.';
    }
}

final class PostState extends BaseState {
    /**
     * Nombre del estado.
     * @return string
     */
    public function name(): string { return 'post'; }
    
    /**
     * Texto descriptivo del estado.
     * @return string
     */
    public function getText(): string {
        return 'Estado POST vía fetch/AJAX.';
    }
}