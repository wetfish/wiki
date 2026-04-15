<?php

// Only bootstrap if the vendor autoload is present (i.e. composer install has run)
if (!file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    return;
}

use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\API\Trace\StatusCode;
use OpenTelemetry\SDK\SdkAutoConfiguration;

require_once dirname(__DIR__) . '/vendor/autoload.php';

SdkAutoConfiguration::initialize();

$tracer = Globals::tracerProvider()->getTracer('wiki', '1.0.0');

$_otelRootSpan = $tracer->spanBuilder('http ' . ($_SERVER['REQUEST_METHOD'] ?? 'GET'))
    ->setSpanKind(SpanKind::KIND_SERVER)
    ->setAttribute('http.method', $_SERVER['REQUEST_METHOD'] ?? 'GET')
    ->setAttribute('http.target', $_SERVER['REQUEST_URI'] ?? '/')
    ->setAttribute('http.host', $_SERVER['HTTP_HOST'] ?? '')
    ->startSpan();

$_otelRootScope = $_otelRootSpan->activate();

register_shutdown_function(function() {
    global $_otelRootSpan, $_otelRootScope;
    if (!isset($_otelRootSpan)) return;
    $status = http_response_code() ?: 200;
    $_otelRootSpan->setAttribute('http.status_code', $status);
    if ($status >= 500) {
        $_otelRootSpan->setStatus(StatusCode::STATUS_ERROR);
    }
    $_otelRootSpan->end();
    $_otelRootScope->detach();
});
