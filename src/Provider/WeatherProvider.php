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
 * Weather provider
 */
class WeatherProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(): string
    {
        return 'Пог+ода отл+ичная!';
    }
}
