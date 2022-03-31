<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Command;

use SvenPetersen\Instagram\Service\InstagramService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class AccessTokenRefresherCommand extends Command
{
    private InstagramService $instagramService;

    public function __construct(
        InstagramService $instagramService,
        $name = null
    ) {
        parent::__construct($name);

        $this->instagramService = $instagramService;
    }

    protected function configure()
    {
        $this
            ->setHelp('Refreshes the API Access Tokens for all users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->instagramService->refreshAllAccessTokens();

        return self::SUCCESS;
    }
}
