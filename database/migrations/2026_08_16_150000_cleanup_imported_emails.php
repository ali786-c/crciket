<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $users = User::query()
            ->where('email', 'like', 'player+%')
            ->orWhere('email', 'like', '%@imported.cricketdraft.local')
            ->get();

        foreach ($users as $user) {
            $profile = $user->playerProfile;
            $phone = $profile?->phone;
            $phoneSuffix = '';
            if ($phone) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                $phoneSuffix = substr($cleanPhone, -4);
            }

            $baseEmail = Str::slug($user->name ?: 'player', '.');
            if ($phoneSuffix !== '') {
                $baseEmail .= '.' . $phoneSuffix;
            }
            $email = $baseEmail . '@cricketdraft.local';

            $counter = 1;
            while (User::query()->where('email', $email)->where('id', '<>', $user->id)->exists()) {
                $email = $baseEmail . '.' . $counter . '@cricketdraft.local';
                $counter++;
            }

            $user->update(['email' => $email]);
        }
    }

    public function down(): void
    {
    }
};
