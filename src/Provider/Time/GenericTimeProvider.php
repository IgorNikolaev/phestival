<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Igor Nikolaev
 * @link      http://www.penguin33.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phestival\Provider\Time;

use Phestival\Provider\ProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Generic time provider
 */
class GenericTimeProvider implements ProviderInterface
{
    /**
     * @var \NumberFormatter
     */
    private $feminineNumberFormatter;

    /**
     * @var \NumberFormatter
     */
    private $masculineNumberFormatter;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \NumberFormatter                                   $feminineNumberFormatter  Feminine number formatter
     * @param \NumberFormatter                                   $masculineNumberFormatter Masculine number formatter
     * @param \Symfony\Component\Translation\TranslatorInterface $translator               Translator
     */
    public function __construct(
        \NumberFormatter $feminineNumberFormatter,
        \NumberFormatter $masculineNumberFormatter,
        TranslatorInterface $translator
    ) {
        $this->feminineNumberFormatter = $feminineNumberFormatter;
        $this->masculineNumberFormatter = $masculineNumberFormatter;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function get(): string
    {
        return $this->translator->trans('provider.time.common.speech', [
            '%time%' => implode(' ', [$this->getHours(), $this->getMinutes()]),
        ]);
    }

    /**
     * @return string
     */
    private function getHours(): string
    {
        $number = (int)(new \DateTimeImmutable())->format('G');

        return $this->translator->transChoice('provider.time.common.hours', $number, [
            '%number%' => $this->masculineNumberFormatter->format($number),
        ]);
    }

    /**
     * @return string
     */
    private function getMinutes(): string
    {
        $number = (int)(new \DateTimeImmutable())->format('i');

        return 0 === $number
            ? $this->translator->trans('provider.time.common.exactly')
            : $this->translator->transChoice('provider.time.common.minutes', $number, [
                '%number%' => $this->feminineNumberFormatter->format($number),
            ]);
    }
}
