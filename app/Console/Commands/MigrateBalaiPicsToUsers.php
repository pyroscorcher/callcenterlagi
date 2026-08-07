<?php

namespace App\Console\Commands;

use App\Models\Balai;
use App\Models\Pic;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateBalaiPicsToUsers extends Command
{
    protected $signature = 'balai:migrate-pics-to-users';
    protected $description = 'Copy Balai credentials + PIC rows into users (role=pic)';

    public function handle(): int
    {
        $generatedPasswords = [];

        DB::transaction(function () use (&$generatedPasswords) {
            Balai::orderBy('id')->get()->each(function (Balai $balai) use (&$generatedPasswords) {
                $pics = Pic::where('balai_id', $balai->id)->orderBy('id')->get();

                if ($pics->isEmpty()) {
                    // No PIC rows recorded — still give the Balai one working login.
                    $username = "{$balai->username}-1";

                    User::updateOrCreate(
                        ['username' => $username],
                        [
                            'name'     => $balai->kepala ?: $balai->nama_balai,
                            'kontak'   => $balai->kontak,
                            'password' => $balai->password, // already hashed, won't be re-hashed
                            'role'     => 'pic',
                            'balai_id' => $balai->id,
                        ]
                    );
                    return;
                }

                foreach ($pics as $index => $pic) {
                    $number   = $index + 1;
                    $username = "{$balai->username}-{$number}";
                    $isFirst  = $number === 1;

                    // First PIC inherits the real, already-hashed password.
                    // Everyone else gets a brand-new one — there's no prior credential for them.
                    $password = $isFirst ? $balai->password : Str::password(12);

                    User::updateOrCreate(
                        ['username' => $username],
                        [
                            'name'     => $pic->nama,
                            'kontak'   => $pic->kontak,
                            'password' => $password,
                            'role'     => 'pic',
                            'balai_id' => $balai->id,
                        ]
                    );

                    if (! $isFirst) {
                        $generatedPasswords[] = [$username, $password];
                    }
                }
            });
        });

        $this->info('Migration complete.');

        if ($generatedPasswords) {
            $this->warn('These accounts got brand-new passwords — distribute securely, then force a reset:');
            $this->table(['username', 'temporary_password'], $generatedPasswords);
        }

        return self::SUCCESS;
    }
}