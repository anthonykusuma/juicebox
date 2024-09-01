<?php

namespace App\Console\Commands;

use App\Jobs\SendWelcomeEmail as SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SendWelcomeEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send-welcome {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a welcome email to the specified user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');

        try {
            $user = User::findOrFail($userId);

            SendWelcomeEmailJob::dispatch($user);

            $this->info('Welcome email sent to user ID: '.$userId);
        } catch (ModelNotFoundException $e) {
            $this->error('User not found.');
        }

        return 0;
    }
}
