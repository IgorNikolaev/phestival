<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Igor Nikolaev
 * @link      http://www.penguin33.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phestival\Command;

use Phestival\Provider\ProviderPool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Speak command
 */
class SpeakCommand extends Command
{
    /**
     * @var \Phestival\Provider\ProviderPool
     */
    private $providerPool;

    /**
     * @var string
     */
    private $language;

    /**
     * @param string                           $name         Command name
     * @param \Phestival\Provider\ProviderPool $providerPool Provider pool
     * @param string                           $language     Language
     */
    public function __construct(string $name, ProviderPool $providerPool, string $language)
    {
        parent::__construct($name);

        $this->providerPool = $providerPool;
        $this->language = $language;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = $this->providerPool->getText();

        $output->writeln($text);

        (new Process(sprintf('/bin/echo "%s" | /usr/bin/festival --tts --language %s', $text, $this->language)))->mustRun();
    }
}
