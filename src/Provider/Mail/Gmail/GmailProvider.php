<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Igor Nikolaev
 * @link      http://www.penguin33.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phestival\Provider\Mail\Gmail;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Phestival\Provider\ProviderInterface;

/**
 * Gmail mail provider
 */
class GmailProvider implements ProviderInterface
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @param \GuzzleHttp\ClientInterface $httpClient HTTP client
     * @param string                      $uri        Gmail Atom feed URI
     * @param string                      $username   Gmail account username
     * @param string                      $password   Gmail account app password
     */
    public function __construct(ClientInterface $httpClient, string $uri, string $username, string $password)
    {
        $this->httpClient = $httpClient;
        $this->uri = $uri;
        $this->username = $username;
        $this->password = $password;

        libxml_use_internal_errors(true);
    }

    /**
     * {@inheritdoc}
     */
    public function get(): string
    {
        $response = $this->httpClient->request('get', $this->uri, [
            RequestOptions::AUTH => [$this->username, $this->password],
        ])->getBody()->getContents();

        $xml = simplexml_load_string($response);

        if (!$xml) {
            $error = libxml_get_last_error();
            $message = $error instanceof \LibXMLError ? $error->message : null;

            throw new \RuntimeException(sprintf('Unable to parse response as XML: "%s" (response: "%s").', $message, $response));
        }

        $xml = (array) $xml;

        if (!isset($xml['fullcount'])) {
            throw new \RuntimeException(
                sprintf('Unable to get new e-mail count: response does not contain element "fullcount" (response: "%s").', $response)
            );
        }

        return $xml['fullcount'];
    }
}
