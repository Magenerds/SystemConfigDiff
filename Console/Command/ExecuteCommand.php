<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace Magenerds\SystemDiff\Console\Command;

use Magenerds\SystemDiff\Helper\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magenerds\SystemDiff\Service\PerformSystemDiffService;

class ExecuteCommand extends Command
{
    /**
     * Holds the command name
     */
    const COMMAND_NAME = 'system-diff:execute';

    /**
     * Holds the command description
     */
    const COMMAND_DESCRIPTION = 'system-diff:execute';

    /**
     * Exit code when exception occurred
     */
    const EXIT_CODE_EXCEPTION = 4;

    /**
     * @var PerformSystemDiffService
     */
    private $performSystemDiffService;

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * ExecuteCommand constructor.
     *
     * @param PerformSystemDiffService $performSystemDiffService
     * @param Config                   $configHelper
     */
    public function __construct(
        PerformSystemDiffService $performSystemDiffService,
        Config $configHelper
    ) {
        parent::__construct(self::COMMAND_NAME);
        $this->performSystemDiffService = $performSystemDiffService;
        $this->configHelper = $configHelper;
    }

    /**
     * Configures the current command.
     */
    public function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);

        parent::configure();
    }

    /**
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return int 0 if everything went fine, or an error code
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $exitStatus = 0;
        try {
            $output->write('Performing sync and diff...');
            $this->performSystemDiffService->performDiff();
            $output->writeln(
                sprintf(
                    'Done at %s.',
                    $this->configHelper->getLastSyncDatetimeFormatted()
                )
            );
        } catch (\Exception $e) {
            $exitStatus = self::EXIT_CODE_EXCEPTION;
            $output->writeln(sprintf('An error occurred during diff: %s', $e->getMessage()));
        }

        return $exitStatus;
    }
}