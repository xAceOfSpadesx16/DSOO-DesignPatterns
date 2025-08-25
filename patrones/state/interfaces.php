<?php

namespace State\Interfaces;


interface StateInterface {
    public function name(): string;
    public function apply(Stateful $obj): void;

    public function getText(): string;
}


interface Stateful{
    public function getState(): StateInterface;
    public function applyState(StateInterface $state): void;

}