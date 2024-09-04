<?php

namespace App\AdminBundle\Command;

use App\CoreBundle\Service\NewsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendWeeklyNewsCommand extends Command
{
    private NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:send-weekly-newsletter')
            ->setDescription('Send weekly newsletter with top news by comments');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->newsService->sendWeeklyNewsletter();

        $output->writeln('Weekly newsletter sent successfully.');

        return Command::SUCCESS;
    }
}