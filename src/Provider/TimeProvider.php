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

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Time provider
 */
class TimeProvider implements ProviderInterface
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
        $hours   = $this->getHours();
        $minutes = $this->getMinutes();

        return $this->translator->trans('provider.time.speech', [
            '%hours%'   => $hours,
            '%minutes%' => $minutes,
        ]);
    }

    /**
     * @return string
     */
    private function getHours(): string
    {
        $number = (int) (new \DateTimeImmutable())->format('G');

        return $this->translator->transChoice('provider.time.hours', $number, [
            '%number%' => $this->masculineNumberFormatter->format($number),
        ]);
    }

    /**
     * @return string
     */
    private function getMinutes(): string
    {
        $number = (int) (new \DateTimeImmutable())->format('i');

        return 0 === $number
            ? $this->translator->trans('provider.time.exactly')
            : $this->translator->transChoice('provider.time.minutes', $number, [
                '%number%' => $this->feminineNumberFormatter->format($number),
            ]);
    }
}
