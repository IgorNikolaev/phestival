#!/usr/bin/env php
<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Igor Nikolaev
 * @link      http://www.penguin33.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$container = new ContainerBuilder();
$container->setParameter('project_dir', __DIR__);
(new YamlFileLoader($container, new FileLocator(__DIR__.'/config')))->load('services.yml');
$container->compile();

$app = new Application();

foreach ($container->findTaggedServiceIds('command') as $id => $attr) {
    $app->add($container->get($id));
}

$app->run();
