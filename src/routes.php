<?php

use Silex\Application;


$app->mount( '/', new Controller\TicketController());

$app->mount( '/acme', new Controller\AcmeController());