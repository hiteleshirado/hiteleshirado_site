<?php

// Registering model classes autoloading.
$app['autoloader']->registerNamespace( 'News',  __DIR__ . '/../models' );
$app['autoloader']->registerNamespace( 'Auth',  __DIR__ . '/../models' );