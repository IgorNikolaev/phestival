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
 * OpenWeatherMap current weather data API response wind
 */
class Wind
{
    private const DIRECTIONS = [
        'n'  => [[318, 360], [0, 22]],
        'ne' => [[22, 68]],
        'e'  => [[68, 112]],
        'se' => [[112, 158]],
        's'  => [[158, 202]],
        'sw' => [[202, 248]],
        'w'  => [[248, 292]],
        'nw' => [[292, 318]],
    ];

    /**
     * @required
     *
     * @var float
     */
    private $speed;

    /**
     * @required
     *
     * @var int
     */
    private $deg;

    /**
     * @return int
     */
    public function getSpeed(): int
    {
        return (int) round($this->speed);
    }

    /**
     * @return string|null
     */
    public function getDirection(): ?string
    {
        foreach (self::DIRECTIONS as $direction => $ranges) {
            foreach ($ranges as list($min, $max)) {
                if ($this->deg >= $min && $this->deg <= $max) {
                    return $direction;
                }
            }
        }

        return null;
    }
}
