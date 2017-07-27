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

/**
 * Provider pool
 */
class ProviderPool
{
    /**
     * @var \Phestival\Provider\ProviderInterface[]
     */
    private $providers;

    /**
     * Provider pool constructor.
     */
    public function __construct()
    {
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
    public function getText(): string
    {
        $parts = [];

        foreach ($this->providers as $provider) {
            $parts[] = $provider->get();
        }

        return trim(implode(' ', $parts));
    }
}
