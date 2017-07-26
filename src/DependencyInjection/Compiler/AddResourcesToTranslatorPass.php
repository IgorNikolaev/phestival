<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Igor Nikolaev
 * @link      http://www.penguin33.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phestival\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

/**
 * Add resources to the translator compiler pass
 */
class AddResourcesToTranslatorPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $projectDir;

    /**
     * @param string $projectDir Project directory
     */
    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('translator');

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ((new Finder())->in($this->projectDir.'/resources/translations')->name('/^\w+\.\w+$/')->files() as $file) {
            list($locale, $format) = explode('.', $file->getFilename());

            $definition->addMethodCall('addResource', [$format, $file->getPathname(), $locale]);
        }
    }
}
