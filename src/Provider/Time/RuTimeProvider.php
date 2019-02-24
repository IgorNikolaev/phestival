<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Igor Nikolaev
 * @link      http://www.penguin33.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phestival\Provider\Time;

use Phestival\Provider\ProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Russian time provider
 */
class RuTimeProvider implements ProviderInterface
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
            '%time%' => $this->getTime(),
        ]);
    }

    /**
     * @return string
     */
    private function getTime(): string
    {
        $now = new \DateTimeImmutable();

        $hours   = (int)$now->format('g');
        $minutes = (int)round((int)$now->format('i') / 5) * 5;

        if (0 === $minutes) {
            $hoursFormatted = 1 === $hours
                ? $this->translator->trans('provider.time.ru.hours.one')
                : $this->translator->transChoice('provider.time.common.hours', $hours, [
                    '%number%' => $this->masculineNumberFormatter->format($hours),
                ]);

            return implode(' ', [
                $hoursFormatted,
                $this->translator->trans('provider.time.common.exactly'),
            ]);
        }

        $hours++;

        if ($hours > 12) {
            $hours = 1;
        }
        if ($minutes < 30) {
            return implode(' ', [
                $this->translator->transChoice('provider.time.common.minutes', $minutes, [
                    '%number%' => $this->feminineNumberFormatter->format($minutes),
                ]),
                $this->translator->trans(sprintf('provider.time.ru.hours.genitive.%d', $hours)),
            ]);
        }
        if ($minutes > 30) {
            $hoursFormatted = 1 === $hours
                ? $this->translator->trans('provider.time.ru.hours.one')
                : $this->masculineNumberFormatter->format($hours);

            return implode(' ', [
                $this->translator->trans('provider.time.ru.minutes.without', [
                    '%number%' => $this->translator->trans(sprintf('provider.time.ru.minutes.genitive.%d', 60 - $minutes)),
                ]),
                $hoursFormatted,
            ]);
        }

        return $this->translator->trans('provider.time.ru.hours.half', [
            '%number%' => $this->translator->trans(sprintf('provider.time.ru.hours.genitive.%d', $hours)),
        ]);
    }
}
