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

/**
 * Time provider
 */
class TimeProvider implements ProviderInterface
{
    private const GENDER_FEMININE  = 'feminine';
    private const GENDER_MASCULINE = 'masculine';

    /**
     * @var \NumberFormatter
     */
    private $numberFormatter;

    /**
     * @param string $locale Locale
     */
    public function __construct(string $locale)
    {
        $this->numberFormatter = new \NumberFormatter($locale, \NumberFormatter::SPELLOUT);
    }

    /**
     * {@inheritdoc}
     */
    public function get(): string
    {
        $parts = [
            $this->getHours(),
            $this->getMinutes(),
        ];

        return implode(' ', $parts);
    }

    /**
     * @return string
     */
    private function getHours(): string
    {
        $number = (int) (new \DateTimeImmutable())->format('H');

        return $this->formatNumber($number);
    }

    /**
     * @return string
     */
    private function getMinutes(): string
    {
        $number = (int) (new \DateTimeImmutable())->format('i');

        return $this->formatNumber($number, self::GENDER_FEMININE);
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
