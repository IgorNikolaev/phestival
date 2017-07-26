<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Igor Nikolaev
 * @link      http://www.penguin33.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phestival\Provider\Weather\OWM;

use GuzzleHttp\ClientInterface;
use Phestival\Provider\ProviderInterface;
use Phestival\Provider\Weather\OWM\Response\Main;
use Phestival\Provider\Weather\OWM\Response\Response;
use Phestival\Provider\Weather\OWM\Response\Wind;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * OpenWeatherMap weather provider
 */
class OWMWeatherProvider implements ProviderInterface
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $httpClient;

    /**
     * @var \JsonMapper
     */
    private $jsonMapper;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $uri;

    /**
     * @param \GuzzleHttp\ClientInterface                        $httpClient HTTP client
     * @param \JsonMapper                                        $jsonMapper JSON mapper
     * @param \Symfony\Component\Translation\TranslatorInterface $translator Translator
     * @param string                                             $uri        OpenWeatherMap current weather data API URI
     */
    public function __construct(ClientInterface $httpClient, \JsonMapper $jsonMapper, TranslatorInterface $translator, string $uri)
    {
        $this->httpClient = $httpClient;
        $this->jsonMapper = $jsonMapper;
        $this->translator = $translator;
        $this->uri = $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function get(): string
    {
        $json = $this->httpClient->request('get', $this->uri)->getBody()->getContents();

        $data = json_decode($json);

        if (null === $data) {
            throw new \RuntimeException(
                sprintf('Unable to decode response as JSON: "%s" (response: "%s").', json_last_error_msg(), $json)
            );
        }

        $response = $this->createResponse($data);

        return $this->translator->trans('provider.weather.owm.text', [
            '%description%' => $response->getWeather()->getDescription(),
            '%wind%'        => $this->translateWind($response->getWind()),
            '%temperature%' => $this->translateTemperature($response->getMain()),
        ]);
    }

    /**
     * @param \Phestival\Provider\Weather\OWM\Response\Main $main Main
     *
     * @return string
     */
    private function translateTemperature(Main $main): string
    {
        $temperature = $main->getTemperature();

        return $this->translator->transChoice('provider.weather.owm.temperature', $temperature, [
            '%number%' => $temperature,
        ]);
    }

    /**
     * @param \Phestival\Provider\Weather\OWM\Response\Wind $wind Wind
     *
     * @return string
     */
    private function translateWind(Wind $wind): string
    {
        $speed = $wind->getSpeed();

        return $this->translator->trans('provider.weather.owm.wind.text', [
            '%direction%' => $this->translator->trans('provider.weather.owm.wind.direction.'.$wind->getDirection()),
            '%speed%'     => $this->translator->transChoice('provider.weather.owm.wind.speed', $speed, [
                '%number%' => $speed,
            ]),
        ]);
    }

    /**
     * @param \stdClass $data Response data
     *
     * @return \Phestival\Provider\Weather\OWM\Response\Response
     */
    private function createResponse(\stdClass $data): Response
    {
        return $this->jsonMapper->map($data, new Response());
    }
}
