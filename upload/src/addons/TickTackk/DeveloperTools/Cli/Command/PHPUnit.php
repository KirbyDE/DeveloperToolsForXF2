<?php

namespace TickTackk\DeveloperTools\Cli\Command;

use PHPUnit\Util\TestDox\TextResultPrinter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Cli\Command\AddOnActionTrait;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestFailure;
use Symfony\Component\Console\Helper\ProgressIndicator;

/**
 * Class Tests
 *
 * @package TickTackk\DeveloperTools\Cli\Command
 */
class PHPUnit extends Command
{
    use AddOnActionTrait;

    protected function configure()
    {
        $this
            ->setName('ticktackk-devtools:phpunit')
            ->setDescription('Runs PHPUnit tests for an add-on')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Add-On ID'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $addOnId = $input->getArgument('id');
        $addOn = $this->checkEditableAddOn($addOnId, $error);
        if (!$addOn)
        {
            $output->writeln('<error>' . $error . '</error>');
            return 1;
        }

        $addOnDirectory = $addOn->getAddOnDirectory();
        $testRoot = $addOnDirectory . DIRECTORY_SEPARATOR . '_tests';
        $phpunit = new \PHPUnit\TextUI\TestRunner();
        $suite = $phpunit->getTest($testRoot, '', 'Test.php');

        if ($suite)
        {
            if (!count($suite->tests()))
            {
                $output->writeln(['', 'No tests available.']);
                return 0;
            }

            $output->writeln(['', 'Running tests...']);

            $testResult = $suite->run();

            $output->writeln(['', 'All tests ran.']);

            if (!$testResult->wasSuccessful())
            {
                if (!empty($testResult->errors()))
                {
                    $this->writeTestDetails($output, 'Tests that have errors:', $testResult->errors());
                }

                if (!empty($testResult->failures()))
                {
                    $this->writeTestDetails($output, 'Tests that have failed:', $testResult->failures());
                }

                if (!empty($testResult->warnings()))
                {
                    $this->writeTestDetails($output, 'Tests that have warnings:', $testResult->failures());
                }

                $output->writeln(['', 'Test failed. Aborting now.']);
                return 1;
            }
        }

        $output->writeln(['', 'All tests passed.', '']);
        return 0;
    }

    /**
     * @param OutputInterface $output
     * @param                 $testFailedMessage
     * @param array           $errors
     */
    protected function writeTestDetails(OutputInterface $output, $testFailedMessage, array $errors)
    {
        if (count($errors))
        {
            $output->writeln(['', $testFailedMessage, '']);

            /** @var \PHPUnit\Framework\TestFailure $error */
            foreach ($errors AS $error)
            {
                $output->writeln($error->getTestName() . ' => ' . $error->exceptionMessage());
            }
        }
    }
}