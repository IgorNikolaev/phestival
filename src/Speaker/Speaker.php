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
     * @var string
     */
    private $tmpFileDir;

    /**
     * @var string|null
     */
    private $tmpFilePathname;

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem        Filesystem
     * @param string                                   $playCommand       Play command
     * @param string                                   $synthesizeCommand Speech synthesize command
     * @param string                                   $tmpFileDir        Directory for storing temporary files
     */
    public function __construct(Filesystem $filesystem, string $playCommand, string $synthesizeCommand, string $tmpFileDir)
    {
        $this->filesystem = $filesystem;
        $this->playCommand = $playCommand;
        $this->synthesizeCommand = $synthesizeCommand;
        $this->tmpFileDir = $tmpFileDir;

        $this->tmpFilePathname = null;

        pcntl_signal(SIGINT, [$this, '__destruct']);
    }

    /**
     * Makes cleanup.
     */
    public function __destruct()
    {
        $this->cleanup();
    }

    /**
     * @param string $speech Speech
     */
    public function speak(string $speech)
    {
        if (!empty($speech)) {
            $this->createTmpFile()->synthesize($speech)->play()->cleanup();
        }
    }

    /**
     * @return self
     */
    private function createTmpFile()
    {
        $this->filesystem->mkdir($this->tmpFileDir);

        $this->tmpFilePathname = $this->filesystem->tempnam($this->tmpFileDir, 'speaker');

        return $this;
    }

    /**
     * @param string $speech Speech
     *
     * @return self
     */
    private function synthesize(string $speech)
    {
        $command = strtr($this->synthesizeCommand, [
            '{SPEECH}' => $speech,
            '{FILE}'   => $this->tmpFilePathname,
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
