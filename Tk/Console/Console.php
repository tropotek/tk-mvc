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
     * @var array
     */
    protected $vendorPaths = array();



    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setInput($input);
        $this->setOutput($output);
        $this->writeInfo(ucwords($this->getName()));
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
     * @return array
     */
    public function getVendorPaths()
    {
        return $this->vendorPaths;
    }

    /**
     * @param array $vendorPaths
     * @return static
     */
    public function setVendorPaths($vendorPaths)
    {
        $this->vendorPaths = $vendorPaths;
        return $this;
    }


    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    protected function writeRed($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<fg=red>%s</>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    protected function writeStrong($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<options=bold>%s</>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    protected function writeInfo($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<info>%s</info>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    protected function writeComment($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<comment>%s</comment>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    protected function writeQuestion($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<question>%s</question>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    protected function writeError($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        return $this->write(sprintf('<error>%s</error>', $str), $options);
    }

    /**
     * @param $str
     * @param int $options
     * @return mixed
     */
    protected function write($str = '', $options = OutputInterface::VERBOSITY_NORMAL)
    {
        if ($this->output)
            return $this->output->writeln($str, $options);
    }
}
