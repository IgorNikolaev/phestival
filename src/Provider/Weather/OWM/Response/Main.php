<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Igor Nikolaev
 * @link      http://www.penguin33.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phestival\Provider\Weather\OWM\Response;

/**
 * OpenWeatherMap current weather data API response main
 */
class Main
{
    /**
     * @required
     *
     * @var float
     */
    private $temp;

    /**
     * @return int
     */
    public function getTemperature(): int
    {
        return (int) round($this->temp);
    }
}
