<?php

namespace App\Services;

use App\ApiUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
class ApiUsageService
{
    /**
     * @param string $username
     */
    function incrementNumberOfApiCallsForUsername(string $username) {

        Cache::add($this->getCacheKeyForUsername($username), 0, Carbon::now()->addMonth());

        Cache::increment($this->getCacheKeyForUsername($username));
    }

    /**
     * Get the prefix for the keys
     *
     * @return string
     */
    protected function getKeyPrefix() {
        return 'api_calls_for_username_'.date('Ym').'_';
    }

    /**
     * @param string $username
     * @return string
     */
    protected function getCacheKeyForUsername(string $username) {
        return $this->getKeyPrefix().$username;
    }

    /**
     * @return array
     */
    function getApiUsageForUsers() {
        $apiUsers = ApiUser::all(['username'])->toArray();

        $mappedUsers = array_map(function($value) {
            return $this->getKeyPrefix().$value;
        }, array_flatten($apiUsers));

        $cacheInfoForMappedUsers = Cache::many($mappedUsers);
        $cacheInfoForUsers = [];

        foreach ($cacheInfoForMappedUsers as $key => $value) {
            $cacheInfoForUsers[str_replace($this->getKeyPrefix(), '', $key)] = $value;
        }

        return $cacheInfoForUsers;
    }
}