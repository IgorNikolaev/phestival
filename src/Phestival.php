<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Igor Nikolaev
 * @link      http://www.penguin33.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phestival;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Phestival
 */
class Phestival
{
    /**
     * @var \Symfony\Component\Console\Application
     */
    private $app;

    /**
     * @param string $projectDir Project directory
     */
    public function __construct(string $projectDir)
    {
        $container = new ContainerBuilder();
        $container->setParameter('project_dir', $projectDir);
        (new YamlFileLoader($container, new FileLocator($projectDir.'/config')))->load('services.yml');
        $container->compile();

        /** @var \Symfony\Component\Console\Command\Command $command */
        $command = $container->get('command.speak');

        $this->app = new Application();
        $this->app->add($command);
    }

    public function run()
    {
        $this->app->run(new ArrayInput(['command' => 'speak']));
    }
}
