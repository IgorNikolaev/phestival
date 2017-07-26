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
use Phestival\Provider\Weather\OWM\Response\Response;

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
     * @var string
     */
    private $uri;

    /**
     * @param \GuzzleHttp\ClientInterface $httpClient HTTP client
     * @param \JsonMapper                 $jsonMapper JSON mapper
     * @param string                      $uri        OpenWeatherMap current weather data API URI
     */
    public function __construct(ClientInterface $httpClient, \JsonMapper $jsonMapper, string $uri)
    {
        $this->httpClient = $httpClient;
        $this->jsonMapper = $jsonMapper;
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

        return $response->getWeather()->getDescription();
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
