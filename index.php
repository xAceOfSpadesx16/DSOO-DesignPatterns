<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/patrones/builder/elements.php';
require_once __DIR__ . '/patrones/builder/builders.php';
require_once __DIR__ . '/patrones/builder/directors.php';
require_once __DIR__ . '/patrones/builder/renderer.php';

use Builder\Builders\TableBuilder;
use Builder\Directors\TableDirector;
use Builder\Renderers\HtmlRenderer;

echo "<h2>Builder / Composite / Iterator</h2>";

// Dataset de ejemplo
$datasetA = [
    'headers' => ['Usuario', 'Comentario', 'Puntaje'],
    'rows' => [
        ['ana', 'Me encantó & lo volvería a comprar', 5],
        ['beto', 'Calidad-precio > promedio', 4],
        ['cami', 'Carita feliz: 😊', 5],
        ['dev',  '<script>alert(\"xss\")</script>', 1],
    ],
    'footers' => ['Promedio', '', 3.75],
];

// Creación de tablas
$builder  = new TableBuilder();
$director = new TableDirector($builder);
$renderer = new HtmlRenderer();

echo "<h3>Dataset A</h3>";
$table = $director->makeFromDataset($datasetA);
echo $renderer->render($table);

echo "<br><br><br>";





echo "<h2>State / Proxy </h2>";

ob_start();
include __DIR__ . '/patrones/state/index.php';
$initialStateHtml = ob_get_clean();
?>
<style>
    .toolbar { display:flex; gap:.5rem; margin:.5rem 0 1rem}
    .card { border:1px solid #e6e6e6; border-radius:10px; padding:8px; margin:4px 0; box-shadow:0 2px 6px rgba(0,0,0,.05); width: 50%; height: 150px;}
</style>

<div class="toolbar">
    <button id="btnFetchState" type="button">Agregar fragmento (GET vía fetch)</button>
    <button id="btnFetchPost"  type="button">Agregar fragmento (POST vía fetch)</button>
    <button id="btnClearState" type="button">Limpiar</button>
</div>

<div id="state-out">
    <div class="card">
        <?= $initialStateHtml ?>
    </div>
</div>

<script>
    const out  = document.getElementById('state-out');
    const btnG = document.getElementById('btnFetchState');
    const btnP = document.getElementById('btnFetchPost');
    const btnC = document.getElementById('btnClearState');

    async function fetchFragment(options = {}) {
        const res  = await fetch('patrones/state/index.php', {
        cache: 'no-store',
        headers: { 'X-Requested-With': 'XMLHttpRequest', ...(options.headers ?? {}) },
        ...options
        });
        const html = await res.text();
        const wrap = document.createElement('div');
        wrap.className = 'card';
        wrap.innerHTML = html;
        out.prepend(wrap);
    }

    btnG.addEventListener('click', () => {
        fetchFragment(); // GET por defecto
    });

    btnP.addEventListener('click', () => {
        fetchFragment({ method: 'POST' });
    });

    btnC.addEventListener('click', () => {
        out.innerHTML = '';
    });
</script>
<?php
