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
use Phestival\Speaker\Speaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
     * @var \Phestival\Speaker\Speaker
     */
    private $speaker;

    /**
     * @param string                           $name         Command name
     * @param \Phestival\Provider\ProviderPool $providerPool Provider pool
     * @param \Phestival\Speaker\Speaker       $speaker      Speaker
     */
    public function __construct(string $name, ProviderPool $providerPool, Speaker $speaker)
    {
        parent::__construct($name);

        $this->providerPool = $providerPool;
        $this->speaker = $speaker;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = $this->providerPool->getText();

        $output->writeln($text);

        $this->speaker->speak($text);
    }
}
