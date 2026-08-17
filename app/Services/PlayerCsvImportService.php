<?php

namespace App\Services;

use App\Models\PlayerProfile;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\User;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PlayerCsvImportService
{
    private const REQUIRED_HEADERS = ['full_name', 'phone', 'location', 'playing_role'];
    private const ROLES = ['Batter', 'Bowler', 'All-rounder', 'Wicketkeeper'];

    public function __construct(private readonly DatabaseManager $database)
    {
    }

    public function import(Tournament $tournament, UploadedFile $file, int $adminId): array
    {
        $rows = $this->parse($file);
        $this->validateRows($rows);

        return $this->database->transaction(function () use ($tournament, $rows, $adminId) {
            $created = 0;
            $updated = 0;
            $registrations = 0;
            foreach ($rows as $row) {
                $profile = PlayerProfile::query()
                    ->with('user')
                    ->where('phone', $row['phone'])
                    ->where('full_name', $row['full_name'])
                    ->first();
                $user = $profile?->user;
                if ($user) {
                    $updated++;
                    $user->update(['name' => $row['full_name']]);
                } else {
                    $created++;
                    $baseEmail = Str::slug($row['full_name'], '.');
                    $cleanPhone = preg_replace('/[^0-9]/', '', $row['phone']);
                    $phoneSuffix = substr($cleanPhone, -4);
                    if ($phoneSuffix !== '') {
                        $baseEmail .= '.' . $phoneSuffix;
                    }
                    $email = $baseEmail . '@cricketdraft.local';

                    $counter = 1;
                    while (User::query()->where('email', $email)->exists()) {
                        $email = $baseEmail . '.' . $counter . '@cricketdraft.local';
                        $counter++;
                    }

                    $user = User::create([
                        'name' => $row['full_name'],
                        'email' => $email,
                        'password' => Str::random(40),
                    ]);
                }
                if (! $user->hasRole('player')) {
                    $user->assignRole('player');
                }

                $profile = PlayerProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'full_name' => $row['full_name'],
                        'phone' => $row['phone'],
                        'city' => $row['location'],
                        'playing_role' => $row['playing_role'],
                        'is_active' => true,
                    ]
                );

                $registration = TournamentPlayer::updateOrCreate(
                    ['tournament_id' => $tournament->id, 'player_profile_id' => $profile->id],
                    [
                        'status' => 'approved',
                        'reviewed_by' => $adminId,
                        'reviewed_at' => now(),
                        'review_notes' => 'Added directly by admin CSV import.',
                    ]
                );
                if ($registration->wasRecentlyCreated) {
                    $registrations++;
                }
            }

            return compact('created', 'updated', 'registrations');
        });
    }

    /** @return array<int, array<string, string>> */
    private function parse(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if ($handle === false) {
            $this->fail('csv', 'The uploaded CSV file could not be opened.');
        }

        $headers = fgetcsv($handle);
        if (! is_array($headers)) {
            fclose($handle);
            $this->fail('csv', 'The CSV file is empty.');
        }
        $headers[0] = preg_replace('/^\\xEF\\xBB\\xBF/', '', (string) $headers[0]);
        $headers = array_map(fn ($header) => strtolower(trim((string) $header)), $headers);
        if ($headers !== self::REQUIRED_HEADERS) {
            fclose($handle);
            $this->fail('csv', 'Invalid CSV headers. Download the demo template and keep the column order unchanged.');
        }

        $rows = [];
        while (($values = fgetcsv($handle)) !== false) {
            if (count($values) === 1 && trim((string) $values[0]) === '') continue;
            $values = array_pad($values, count($headers), '');
            $rows[] = array_combine($headers, array_map(fn ($value) => trim((string) $value), array_slice($values, 0, count($headers))));
            if (count($rows) > 500) {
                fclose($handle);
                $this->fail('csv', 'A maximum of 500 players can be imported in one file.');
            }
        }
        fclose($handle);
        if ($rows === []) $this->fail('csv', 'The CSV file does not contain any player rows.');
        return $rows;
    }

    /** @param array<int, array<string, string>> $rows */
    private function validateRows(array $rows): void
    {
        $errors = [];
        $seenEmails = [];
        foreach ($rows as $index => $row) {
            $line = $index + 2;
            if ($row['full_name'] === '') $errors[] = "Row {$line}: full_name is required.";
            if ($row['phone'] === '') $errors[] = "Row {$line}: phone is required.";
            if ($row['phone'] !== '' && ! preg_match('/^[0-9+()\\-\\s]{7,30}$/', $row['phone'])) $errors[] = "Row {$line}: phone format is invalid.";
            if ($row['location'] === '') $errors[] = "Row {$line}: location is required.";
            $phone = preg_replace('/\\s+/', '', $row['phone']);
            if (isset($seenEmails[$phone])) $errors[] = "Row {$line}: duplicate phone appears in the CSV.";
            $seenEmails[$phone] = true;
            if (! in_array($row['playing_role'], self::ROLES, true)) $errors[] = "Row {$line}: playing_role must be Batter, Bowler, All-rounder, or Wicketkeeper.";
        }
        if ($errors !== []) $this->fail('csv', implode(' ', $errors));
    }

    private function fail(string $key, string $message): never
    {
        throw ValidationException::withMessages([$key => $message]);
    }
}
