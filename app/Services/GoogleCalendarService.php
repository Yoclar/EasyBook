<?php
namespace App\Services;
use Google\Client;
use Google\Service\Calendar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService {
    protected $client;
    protected $calendarService;


    public function __construct(){
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(Calendar::CALENDAR);
        $this->calendarService = new Calendar($this->client);
  
    }

    public function getClient($accessToken = null, $refreshToken = null, $expiresAt = null, $user = null)
    {
  

        if ($accessToken) {
            $this->client->setAccessToken([
                'access_token' => $accessToken,
                'expires_in' => Carbon::parse($expiresAt)->diffInSeconds(now('Europe/Budapest')),
                'refresh_token' => $refreshToken,
            ]);

   
        } else {
            $user = $user ?: auth()->user();
            if (!$user->google_access_token) {
                abort(403, 'Google token missing.');
            }
            $this->client->setAccessToken([
                'access_token' => $user->google_access_token,
                'expires_in' => Carbon::parse($user->google_token_expires_at)->diffInSeconds(now('Europe/Budapest')),
                'refresh_token' => $user->google_refresh_token,
            ]);
            $refreshToken = $user->google_refresh_token; // ha onnan jön

        }
  
        
        // Token frissítés
        if ($this->client->isAccessTokenExpired() && $refreshToken) {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
        
            if (isset($newToken['access_token']) && $user) {
                $user->update([
                    'google_access_token' => $newToken['access_token'],
                    'google_token_expires_at' => now('Europe/Budapest')->addSeconds($newToken['expires_in']),
                ]);
            }
        }
        $this->calendarService = new Calendar($this->client);

        return $this->calendarService;
    }

   


    public function createEvent($eventData, $calendarId = 'primary')
    {
    
        $event = new \Google\Service\Calendar\Event($eventData);
        return $this->calendarService->events->insert($calendarId, $event);
    }
}