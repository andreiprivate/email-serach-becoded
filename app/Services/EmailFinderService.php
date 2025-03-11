<?php

namespace App\Services;

use App\Models\Profile;
use Illuminate\Support\Facades\Http;
use App\Models\Email;

class EmailFinderService
{
    protected $providers;
    protected $authUrl;
    protected $apiKey;
    protected $authCredentials;

    public function __construct()
    {
        $this->providers = config('api.providers');
        $this->authUrl = config('api.auth_url');
        $this->apiKey = config('api.api_key');
        $this->authCredentials = config('api.auth_credentials');
    }

    /**
     * GET API Token
     */
    public function getAuthToken()
    {
        $token = config('api.token');
        if ($token) {
            return $token;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->post($this->authUrl, [
            'email' => $this->authCredentials['email'],
            'password' => $this->authCredentials['password'],
        ]);

        if ($response->successful()) {
            $token = $response->json()['accessToken'] ?? null;

            if ($token) {
                config(['api.token' => $token]);
                return $token;
            }

            throw new \Exception('Authentication successful but no token received.');
        }

        throw new \Exception('API authentication failed. Response: ' . $response->body());
    }

    public function searchEmails($name, $company, $linkedinUrl)
    {
        // Retrieve or create the profile; default tries is set to 1.
        $profile = Profile::firstOrCreate(
            ['name' => $name, 'company' => $company, 'linkedin_url' => $linkedinUrl],
            ['tries' => 1]
        );

        // Check if profile has exhausted all provider attempts.
        if ($profile->tries > count($this->providers)) {
            \Log::info("Profile ID {$profile->id} is no longer searchable. Tries: {$profile->tries}");
            $storedEmails = Email::where('profile_id', $profile->id)->pluck('email')->toArray();
            return ['emails' => $storedEmails, 'message' => 'Profile no longer searchable.'];
        }

        try {
            $token = $this->getAuthToken();
        } catch (\Exception $e) {
            \Log::error("Failed to retrieve auth token for profile ID {$profile->id}: " . $e->getMessage());
            $storedEmails = Email::where('profile_id', $profile->id)->pluck('email')->toArray();
            return ['emails' => $storedEmails, 'message' => 'Unable to retrieve authentication token.'];
        }

        $headers = ['Authorization' => "Bearer $token"];

        foreach ($this->providers as $k => $provider) {
            // Skip providers that have already been attempted.
            if ($k < $profile->tries - 1) {
                continue;
            }

            // Attempt to query the provider.
            try {
                $response = Http::withHeaders($headers)->get($provider['url'], [
                    'name'                => $name,
                    'company'             => $company,
                    'linkedInProfileUrl'  => $linkedinUrl,
                ]);
            } catch (\Exception $e) {
                \Log::error("Error querying provider {$provider['url']} for profile ID {$profile->id}: " . $e->getMessage());
                $profile->tries = $k + 1;
                $profile->save();
                continue;
            }

            $profile->tries = $k + 1;
            $profile->save();

            if ($response->successful()) {
                $emails = $response->json()['emails'] ?? [];
                if (!empty($emails)) {
                    // Save the newly found emails.
                    foreach ($emails as $email) {
                        try {
                            Email::firstOrCreate([
                                'profile_id' => $profile->id,
                                'email'      => $email
                            ]);
                        } catch (\Exception $e) {
                            \Log::error("Error saving email '{$email}' for profile ID {$profile->id}: " . $e->getMessage());
                        }
                    }
                    \Log::info("Provider {$provider['url']} returned valid emails for profile ID {$profile->id}: " . implode(', ', $emails));
                    // Combine these with all previously stored emails.
                    $allEmails = Email::where('profile_id', $profile->id)->pluck('email')->toArray();
                    return ['emails' => $allEmails];
                } else {
                    \Log::warning("Provider {$provider['url']} did not return any emails for profile ID {$profile->id}.");
                }
            } else {
                \Log::error("Provider {$provider['url']} returned unsuccessful response for profile ID {$profile->id}. Status code: {$response->status()}");
            }
        }

        // If no new emails were found, return any emails already stored in the DB.
        $storedEmails = Email::where('profile_id', $profile->id)->pluck('email')->toArray();
        if (!empty($storedEmails)) {
            return ['emails' => $storedEmails];
        }

        \Log::info("No valid emails found for profile ID {$profile->id} after querying all providers.");
        return ['message' => 'No emails found from this profile.'];
    }
}
