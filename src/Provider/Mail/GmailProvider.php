<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Igor Nikolaev
 * @link      http://www.penguin33.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phestival\Provider\Mail;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Phestival\Provider\ProviderInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Gmail mail provider
 */
class GmailProvider implements ProviderInterface
{
    const CACHE_KEY_UNREAD = 'provider.mail.gmail.unread';

    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $cache;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $httpClient;

    /**
     * @var \NumberFormatter
     */
    private $neuterNumberFormatter;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

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
     * @param \Psr\SimpleCache\CacheInterface                    $cache                 Cache
     * @param \GuzzleHttp\ClientInterface                        $httpClient            HTTP client
     * @param \NumberFormatter                                   $neuterNumberFormatter Neuter number formatter
     * @param \Symfony\Component\Translation\TranslatorInterface $translator            Translator
     * @param string                                             $uri                   Gmail Atom feed URI
     * @param string                                             $username              Gmail account username
     * @param string                                             $password              Gmail account app password
     */
    public function __construct(
        CacheInterface $cache,
        ClientInterface $httpClient,
        \NumberFormatter $neuterNumberFormatter,
        TranslatorInterface $translator,
        string $uri,
        string $username,
        string $password
    ) {
        $this->cache = $cache;
        $this->httpClient = $httpClient;
        $this->neuterNumberFormatter = $neuterNumberFormatter;
        $this->translator = $translator;
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

        $unread = (int) $xml['fullcount'];

        $wasUnread = $this->cache->get(self::CACHE_KEY_UNREAD, 0);
        $this->cache->set(self::CACHE_KEY_UNREAD, $unread);

        $new = $unread > $wasUnread ? $unread - $wasUnread : 0;

        $parts = [$this->translateNew($new)];

        if ($unread !== $new) {
            $parts[] = $this->translateUnread($unread);
        }

        return implode(' ', $parts);
    }

    /**
     * @param int $count Count
     *
     * @return string
     */
    private function translateNew(int $count): string
    {
        if (0 === $count) {
            return $this->translator->trans('provider.mail.gmail.new.empty');
        }

        return $this->translator->trans('provider.mail.gmail.new.speech', [
            '%count%' => trim($this->translator->transChoice('provider.mail.gmail.new.count', $count, [
                '%number%' => 1 !== $count ? $this->neuterNumberFormatter->format($count) : '',
            ])),
        ]);
    }

    /**
     * @param int $count Count
     *
     * @return string
     */
    private function translateUnread(int $count): string
    {
        return $this->translator->trans('provider.mail.gmail.unread.speech', [
            '%count%' => $this->translator->transChoice('provider.mail.gmail.unread.count', $count, [
                '%number%' => $this->neuterNumberFormatter->format($count),
            ]),
        ]);
    }
}
