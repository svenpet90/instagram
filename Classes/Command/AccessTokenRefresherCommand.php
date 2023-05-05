<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Command;

use SvenPetersen\Instagram\Service\AccessTokenRefresher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class AccessTokenRefresherCommand extends Command
{
    public function __construct(
        private readonly AccessTokenRefresher $accessTokenRefresher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Refreshes all long-lived API access tokens');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->accessTokenRefresher->refreshAll();

        return Command::SUCCESS;
    }
}
