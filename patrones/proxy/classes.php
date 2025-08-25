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
    public function __construct(private Stateful $subject, private array $server = []) {
        $this->server = $server ?: ($_SERVER ?? []);
        $this->applyAccordingToHeaders();
    }

    public function __call(string $method, array $args): mixed {
        return $this->subject->$method(...$args);
    }

    private function applyAccordingToHeaders(): void {
        $method  = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        $isFetch = strtolower($this->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

        $state = $isFetch
            ? ($method === 'POST' ? new PostState() : new GetState())
            : new DefaultState();

        $state->apply($this->subject);
    }
}