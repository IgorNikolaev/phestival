<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Igor Nikolaev
 * @link      http://www.penguin33.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phestival\Speaker;

use Symfony\Component\Process\Process;

/**
 * Speaker
 */
class Speaker
{
    /**
     * @var string
     */
    private $language;

    /**
     * @param string $language Language
     */
    public function __construct(string $language)
    {
        $this->language = $language;
    }

    /**
     * @param string $text Text
     */
    public function speak(string $text)
    {
        (new Process(sprintf('echo "%s" | festival --tts --language %s', $text, $this->language)))->mustRun();
    }
}
