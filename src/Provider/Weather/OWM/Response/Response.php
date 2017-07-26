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
 * OpenWeatherMap current weather data API response
 */
class Response
{
    /**
     * @required
     *
     * @var \Phestival\Provider\Weather\OWM\Response\Coord
     */
    private $coord;

    /**
     * @required
     *
     * @var \Phestival\Provider\Weather\OWM\Response\Weather[]
     */
    private $weather;

    /**
     * @required
     *
     * @var \Phestival\Provider\Weather\OWM\Response\Main
     */
    private $main;

    /**
     * @required
     *
     * @var \Phestival\Provider\Weather\OWM\Response\Wind
     */
    private $wind;

    /**
     * @required
     *
     * @var string
     */
    private $name;

    /**
     * @return \Phestival\Provider\Weather\OWM\Response\Coord
     */
    public function getCoordinates(): Coord
    {
        return $this->coord;
    }

    /**
     * @return \Phestival\Provider\Weather\OWM\Response\Weather
     */
    public function getWeather(): Weather
    {
        return reset($this->weather);
    }

    /**
     * @return \Phestival\Provider\Weather\OWM\Response\Main
     */
    public function getMain(): Main
    {
        return $this->main;
    }

    /**
     * @return \Phestival\Provider\Weather\OWM\Response\Wind
     */
    public function getWind(): Wind
    {
        return $this->wind;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
