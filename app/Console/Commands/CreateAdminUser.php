<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('admin:create {--name= : Display name} {--email= : Login email} {--password= : Login password (asked for when omitted)}')]
#[Description('Create an admin panel login, or reset the password of an existing one')]
class CreateAdminUser extends Command
{
    public function handle(): int
    {
        $name = $this->option('name') ?: text('Name', default: 'Admin', required: true);
        $email = $this->option('email') ?: text('Login email', required: true);
        $password = $this->option('password') ?: password('Password (min 8 characters)', required: true);

        $validator = Validator::make(compact('email', 'password'), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $user = User::updateOrCreate(['email' => $email], ['name' => $name, 'password' => $password]);

        $this->components->info(($user->wasRecentlyCreated ? 'Admin created' : 'Admin password updated').": {$email}");
        $this->line('  Log in at '.route('admin.login'));

        return self::SUCCESS;
    }
}
