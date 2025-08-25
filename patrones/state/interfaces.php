<?php

namespace State\Interfaces;

/**
 * Contrato para representar un estado y sus operaciones sobre objetos Stateful.
 */
interface StateInterface {
    public function name(): string;
    public function apply(Stateful $obj): void;

    public function getText(): string;
}

/**
 * Contrato para objetos que pueden tener y cambiar de estado.
 */
interface Stateful{
    public function getState(): StateInterface;
    public function applyState(StateInterface $state): void;

}