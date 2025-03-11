<?php

namespace App\Console\Commands;

use App\Services\EmailFinderService;
use Illuminate\Console\Command;

class FindEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(EmailFinderService $emailFinderService)
    {
        $name = $this->ask('Enter the name');
        $company = $this->ask('Enter the company (optional)');
        $linkedinUrl = $this->ask('Enter the LinkedIn URL (optional)');

        $emails = $emailFinderService->searchEmails($name, $company, $linkedinUrl);

        if (!empty($emails)) {
            $this->info("Emails found: " . implode(', ', $emails));
        } else {
            $this->warn("No emails found.");
        }
    }

}
