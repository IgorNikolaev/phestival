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
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Phestival
 */
class Phestival
{
    /**
     * @var array
     */
    private $argv;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|null
     */
    private $container;

    /**
     * @var \Symfony\Component\Console\Application|null
     */
    private $app;

    /**
     * @param array  $argv       An array of parameters from the CLI (in the argv format)
     * @param bool   $debug      Is debug enabled
     * @param string $projectDir Project directory
     */
    public function __construct(array $argv, bool $debug, string $projectDir)
    {
        $this->argv = $argv;
        $this->debug = $debug;
        $this->projectDir = $projectDir;

        $this->cacheDir = $projectDir.'/cache';
        $this->container = $this->app = null;
    }

    /**
     * Builds and runs application.
     */
    public function run()
    {
        $this->getApp()->run(new ArgvInput($this->argv));
    }

    /**
     * @return \Symfony\Component\Console\Application
     */
    private function getApp()
    {
        if (empty($this->app)) {
            $this->app = $this->buildApp();
        }

        return $this->app;
    }

    /**
     * @return \Symfony\Component\Console\Application
     */
    private function buildApp()
    {
        $app = new Application();
        $app->add($this->getSpeakCommand());

        return $app;
    }

    /**
     * @return \Symfony\Component\Console\Command\Command
     */
    private function getSpeakCommand(): Command
    {
        return $this->getContainer()->get('command.speak');
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private function getContainer(): ContainerInterface
    {
        if (empty($this->container)) {
            $cache = new ConfigCache($this->cacheDir.'/container.php', $this->debug);

            if (!$cache->isFresh()) {
                $container = $this->buildContainer();

                $cache->write((new PhpDumper($container))->dump(), $container->getResources());
            }

            require_once $cache->getPath();

            $this->container = new \ProjectServiceContainer();
        }

        return $this->container;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function buildContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();

        foreach ([
            'project_dir' => $this->projectDir,
            'cache_dir'   => $this->cacheDir,
            'debug'       => $this->debug,
        ] as $name => $value) {
            $container->setParameter($name, $value);
        }

        (new YamlFileLoader($container, new FileLocator($this->projectDir.'/config')))->load('services.yml');

        $container
            ->addCompilerPass(new AddProvidersToPoolPass())
            ->addCompilerPass(new AddResourcesToTranslatorPass($this->projectDir))
            ->compile();

        return $container;
    }
}
