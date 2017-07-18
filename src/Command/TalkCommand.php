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

use Phestival\Provider\ProviderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Talk command
 */
class TalkCommand extends Command
{
    /**
     * @var \Phestival\Provider\ProviderInterface
     */
    private $timeProvider;

    /**
     * @param string                                $name         Command name
     * @param string                                $description  Command description
     * @param \Phestival\Provider\ProviderInterface $timeProvider Time provider
     */
    public function __construct(string $name, string $description, ProviderInterface $timeProvider)
    {
        parent::__construct($name);

        $this->setDescription($description);

        $this->timeProvider = $timeProvider;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = $this->timeProvider->get();

        $output->writeln($text);

        (new Process(sprintf('/bin/echo "%s" | /usr/bin/festival --tts --language russian', $text)))->mustRun();
    }
}
