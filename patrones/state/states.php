<?php

namespace State\States;

require_once "interfaces.php";

use State\Interfaces\StateInterface;
use State\Interfaces\Stateful;

abstract class BaseState implements StateInterface {

    public function apply(Stateful $obj): void { 
        $obj->applyState($this);
    }
}


final class DefaultState extends BaseState {
    public function name(): string { return 'default'; }

    public function getText(): string {
        return 'Estado por defecto (navegación normal).';
    }
}

final class GetState extends BaseState {
    public function name(): string { return 'get'; }

    public function getText(): string {
        return 'Estado GET vía fetch/AJAX.';
    }
}

final class PostState extends BaseState {
    public function name(): string { return 'post'; }
    
    public function getText(): string {
        return 'Estado POST vía fetch/AJAX.';
    }
}