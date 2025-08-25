<?php
namespace Proxy;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'state' . DIRECTORY_SEPARATOR . 'interfaces.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'state' . DIRECTORY_SEPARATOR . 'states.php';

use State\Interfaces\Stateful;
use State\States\DefaultState;
use State\States\GetState;
use State\States\PostState;

final class StateProxy
{
    /**
     * Constructor de StateProxy.
     * @param Stateful $subject El objeto sujeto al patrón State.
     * @param array $server Información del servidor (headers, método, etc).
     */
    public function __construct(private Stateful $subject, private array $server = []) {
        $this->server = $server ?: ($_SERVER ?? []);
        $this->applyAccordingToHeaders();
    }

    /**
     * Proxy dinámico para llamadas a métodos del sujeto.
     * @param string $method Nombre del método.
     * @param array $args Argumentos del método.
     * @return mixed
     */
    public function __call(string $method, array $args): mixed {
        return $this->subject->$method(...$args);
    }

    /**
     * Aplica el estado correspondiente según los headers HTTP.
     * @return void
     */
    private function applyAccordingToHeaders(): void {
        $method  = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        $isFetch = strtolower($this->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

        $state = $isFetch
            ? ($method === 'POST' ? new PostState() : new GetState())
            : new DefaultState();

        $state->apply($this->subject);
    }
}