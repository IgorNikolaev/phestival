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

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Speaker
 */
class Speaker
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $playCommand;

    /**
     * @var string
     */
    private $synthesizeCommand;

    /**
     * @var string|null
     */
    private $tmpFilePathname;

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem        Filesystem
     * @param string                                   $playCommand       Play command
     * @param string                                   $synthesizeCommand Speech synthesize command
     */
    public function __construct(Filesystem $filesystem, string $playCommand, string $synthesizeCommand)
    {
        $this->filesystem = $filesystem;
        $this->playCommand = $playCommand;
        $this->synthesizeCommand = $synthesizeCommand;

        $this->tmpFilePathname = null;
    }

    public function __destruct()
    {
        $this->cleanup();
    }

    /**
     * @param string $text Text to speak
     */
    public function speak(string $text)
    {
        if (!empty($text)) {
            $this->createTmpFile()->synthesize($text)->play()->cleanup();
        }
    }

    /**
     * @return self
     */
    private function createTmpFile()
    {
        $this->tmpFilePathname = $this->filesystem->tempnam(sys_get_temp_dir(), 'phestival');

        return $this;
    }

    /**
     * @param string $text Text to speak
     *
     * @return self
     */
    private function synthesize(string $text)
    {
        $command = strtr($this->synthesizeCommand, [
            '{TEXT}' => $text,
            '{FILE}' => $this->tmpFilePathname,
        ]);

        (new Process($command))->mustRun();

        return $this;
    }

    /**
     * @return self
     */
    private function play()
    {
        $command = strtr($this->playCommand, [
            '{FILE}' => $this->tmpFilePathname,
        ]);

        (new Process($command))->mustRun();

        return $this;
    }

    /**
     * @return self
     */
    private function cleanup()
    {
        if (!empty($this->tmpFilePathname)) {
            $this->filesystem->remove($this->tmpFilePathname);
        }

        return $this;
    }
}
