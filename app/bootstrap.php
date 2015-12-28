<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$environment = $configurator->setDebugMode('x.x.x.x');
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$environment = (Nette\Configurator::detectDebugMode('127.0.0.1') or (PHP_SAPI == 'cli' && Nette\Utils\Strings::startsWith(getHostByName(getHostName()), "192.168.")))
    ? 'development'
    : 'production';

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . "/config/config.$environment.neon");

$container = $configurator->createContainer();
$container->getService('application')->errorPresenter = 'Front:Error';

return $container;
