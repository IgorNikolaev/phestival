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
    private const GENDER_FEMININE  = 'feminine';
    private const GENDER_MASCULINE = 'masculine';

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \NumberFormatter
     */
    private $numberFormatter;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator Translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        $this->numberFormatter = new \NumberFormatter($translator->getLocale(), \NumberFormatter::SPELLOUT);
    }

    /**
     * {@inheritdoc}
     */
    public function get(): string
    {
        $hours   = $this->getHours();
        $minutes = $this->getMinutes();

        return $this->translator->trans('provider.time.text', [
            '%hours%'   => $hours,
            '%minutes%' => $minutes,
        ]);
    }

    /**
     * @return string
     */
    private function getHours(): string
    {
        $number = (int) (new \DateTimeImmutable())->format('H');

        return $this->translator->transChoice('provider.time.hours', $number, [
            '%number%' => $this->formatNumber($number),
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
                '%number%' => $this->formatNumber($number, self::GENDER_FEMININE),
            ]);
    }

    /**
     * @param int    $number Number
     * @param string $gender Gender
     *
     * @return string
     */
    private function formatNumber(int $number, string $gender = self::GENDER_MASCULINE): string
    {
        $this->numberFormatter->setTextAttribute(\NumberFormatter::DEFAULT_RULESET, '%spellout-cardinal-'.$gender);

        return $this->numberFormatter->format($number);
    }
}
