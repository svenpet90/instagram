<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Command;

use SvenPetersen\Instagram\Service\AccessTokenRefresher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class AccessTokenRefresherCommand extends Command
{
    private AccessTokenRefresher $accessTokenRefresher;

    public function __construct(
        AccessTokenRefresher $accessTokenRefresher
    ) {
        $this->accessTokenRefresher = $accessTokenRefresher;

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
