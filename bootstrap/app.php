<?php

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Events\Dispatcher;

require __DIR__ . '/../vendor/autoload.php';

$app = new class extends Container {
    public function runningUnitTests() { return false; } 
};

Facade::setFacadeApplication($app);

$app->instance('app', $app);
$app->singleton('files', fn() => new Filesystem());
$app->singleton('events', fn($app) => new Dispatcher($app));
$app->singleton('config', fn() => new ConfigRepository([
    'mode_config' => Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/../App/Src/Configs/ForgeFoundary.yaml')
]));

$app->bind('path.base', fn() => __DIR__ . '/..');
$app->bind('path.config', fn() => __DIR__ . '/../App/Src/Configs');

return $app;