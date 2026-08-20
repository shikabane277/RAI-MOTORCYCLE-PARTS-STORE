<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdminCommand extends Command
{
    protected $signature = 'make:admin {email? : Email address of the user} {--name= : Name of the admin} {--password= : Password for the admin}';

    protected $description = 'Create a new admin user or promote an existing user to admin role';

    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Enter admin email address');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update(['role' => 'admin']);
            $this->info("User '{$user->email}' has been promoted to Admin role successfully!");
            return Command::SUCCESS;
        }

        $name = $this->option('name') ?? $this->ask('Enter admin full name', 'RAI Administrator');
        $password = $this->option('password') ?? $this->secret('Enter admin password');

        if (empty($password)) {
            $this->error('Password cannot be empty.');
            return Command::FAILURE;
        }

        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'admin',
        ]);

        $this->info("Admin account '{$user->email}' created successfully!");
        return Command::SUCCESS;
    }
}
