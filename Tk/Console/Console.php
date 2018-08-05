<?php
namespace Tk\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
abstract class Console extends Command
{

    /**
     * @var OutputInterface
     */
    protected $output = null;

    /**
     * @var InputInterface
     */
    protected $input = null;

    /**
     * @var string
     */
    protected $locFile = '';


    /**
     * Console constructor.
     * @param null|string $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        //$this->locFile = $this->getConfig()->getTempPath().'/'.md5(__FILE__.$this->getName()).'.lock';
        $this->locFile = $this->getConfig()->getTempPath().'/'.$this->getName().'.lock';
    }

    /**
     *
     */
    public function __destruct()
    {
        \Tk\FileLocker::unlockFile($this->locFile);
    }

    /**
     * Initializes the command just after the input has been validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     * @throws \Tk\Exception
     * @throws \Exception
     * @todo: Maybe we need an option for allowing more than one running instance???
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        if (!\Tk\FileLocker::lockFile($this->locFile)) {
            throw new \Tk\Exception('Instance already executing. Aborting.');
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->setInput($input);
        $this->setOutput($output);
        $this->writeInfo($this->getName());
        //$this->writeInfo(ucwords(preg_replace('/([-_]*[A-Z])/', ' $1', $this->getName())));

    }


    /**
     * @return string
     */
    public function getLocFile()
    {
        return $this->locFile;
    }

    /**
     * @return \Tk\Config
     */
    public function getConfig()
    {
        return \Tk\Config::getInstance();
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     * @return static
     */
    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param InputInterface $input
     * @return static
     */
    public function setInput($input)
    {
        $this->input = $input;
        return $this;
    }


    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    public function writeRed($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<fg=red>%s</>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    public function writeGrey($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<fg=white>%s</>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    public function writeBlue($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<fg=blue>%s</>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    public function writeStrongBlue($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<fg=blue;options=bold>%s</>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    public function writeStrong($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<options=bold>%s</>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    public function writeInfo($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<info>%s</info>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    public function writeStrongInfo($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<fg=green;options=bold>%s</>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    public function writeComment($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<comment>%s</comment>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    public function writeQuestion($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<question>%s</question>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    public function writeError($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<error>%s</error>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    public function write($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        if ($this->output)
            return $this->output->writeln($str, $options);
    }
}
