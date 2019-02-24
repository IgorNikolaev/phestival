<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Igor Nikolaev
 * @link      http://www.penguin33.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phestival\Provider;

use Psr\Log\LoggerInterface;

/**
 * Provider pool
 */
class ProviderPool
{
    const EMPHASIS_SYMBOL_DEFAULT = '+';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $emphasisSymbol;

    /**
     * @var \Phestival\Provider\ProviderInterface[]
     */
    private $providers;

    /**
     * @param \Psr\Log\LoggerInterface $logger         Logger
     * @param string                   $emphasisSymbol Emphasis symbol
     */
    public function __construct(LoggerInterface $logger, string $emphasisSymbol)
    {
        $this->logger = $logger;
        $this->emphasisSymbol = $emphasisSymbol;

        $this->providers = [];
    }

    /**
     * @param \Phestival\Provider\ProviderInterface $provider Provider
     */
    public function addProvider(ProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * @return string
     */
    public function getSpeech(): string
    {
        $parts = [];

        foreach ($this->providers as $provider) {
            try {
                $parts[] = $provider->get();
            } catch (\Exception $ex) {
                $context = $ex->getTrace()[0];

                $this->logger->error($ex->getMessage(), [
                    'class'  => $context['class'],
                    'method' => $context['function'],
                ]);
            }
        }

        $speech = trim(implode(' ', $parts));

        if (self::EMPHASIS_SYMBOL_DEFAULT === $this->emphasisSymbol) {
            return $speech;
        }

        return str_replace(self::EMPHASIS_SYMBOL_DEFAULT, $this->emphasisSymbol, $speech);
    }
}
