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

use Phestival\DependencyInjection\Compiler\AddProvidersToPoolPass;
use Phestival\DependencyInjection\Compiler\AddResourcesToTranslatorPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Phestival
 */
class Phestival
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\Console\Application
     */
    private $app;

    /**
     * @param string $projectDir Project directory
     */
    public function __construct(string $projectDir)
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter('project_dir', $projectDir);
        $this->container->setParameter('cache_dir', $projectDir.'/cache');
        (new YamlFileLoader($this->container, new FileLocator($projectDir.'/config')))->load('services.yml');
        $this->container
            ->addCompilerPass(new AddProvidersToPoolPass())
            ->addCompilerPass(new AddResourcesToTranslatorPass($projectDir))
            ->compile();

        $this->app = new Application();
        $this->app->add($this->getSpeakCommand());
    }

    /**
     * @param array $argv An array of parameters from the CLI (in the argv format)
     */
    public function run(array $argv)
    {
        $this->app->run(new ArgvInput($argv));
    }

    /**
     * @return \Symfony\Component\Console\Command\Command
     */
    private function getSpeakCommand(): Command
    {
        return $this->container->get('command.speak');
    }
}
