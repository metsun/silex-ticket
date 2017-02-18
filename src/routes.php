<?php

$app->mount( '/', new Controller\TicketController());

$app->mount( '/acme', new Controller\AcmeController());

