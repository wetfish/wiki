<?php

require_once("config.php");
require_once(__DIR__ . "/telemetry.php");

// Old MySQL API used by most of the project


// New MySQL API recommended for future development
$mysql = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);

require_once "models/model.php";
$model = new Model($mysql);

// Wrapper around mysqli_query that emits an OTel span when tracing is active
function wiki_query($sql) {
    global $mysql, $tracer;

    if (!isset($tracer)) {
        return mysqli_query($mysql, $sql);
    }

    $span = $tracer->spanBuilder('db.query')
        ->setAttribute('db.system', 'mariadb')
        ->setAttribute('db.statement', $sql)
        ->startSpan();
    $scope = $span->activate();

    $result = mysqli_query($mysql, $sql);

    if ($result === false) {
        $span->setAttribute('db.error', mysqli_error($mysql));
        $span->setStatus(\OpenTelemetry\API\Trace\StatusCode::STATUS_ERROR, mysqli_error($mysql));
    }

    $span->end();
    $scope->detach();
    return $result;
}

?>
