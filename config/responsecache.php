<?php

use App\Utils\AppCacheProfile;
use Spatie\ResponseCache\Hasher\DefaultHasher;
use Spatie\ResponseCache\Replacers\CsrfTokenReplacer;
use Spatie\ResponseCache\Serializers\DefaultSerializer;

return [
    /*
     * Determine if the response cache middleware should be enabled.
     */
    'enabled' => env('APP_ENV') !== 'local',

    /*
     *  The given class will determinate if a request should be cached. The
     *  default class will cache all successful GET-requests.
     *
     *  You can provide your own class given that it implements the
     *  CacheProfile interface.
     */
    'cache_profile' => AppCacheProfile::class,

    /*
     * When using the default CacheRequestFilter this setting controls the
     * default number of seconds responses must be cached.
     */
    'cache_lifetime_in_seconds' => 60 * 60 * 24,

    /*
     * This setting determines if a http header named with the cache time
     * should be added to a cached response. This can be handy when
     * debugging.
     */
    'add_cache_time_header' => true,

    /*
     * This setting determines the name of the http header that contains
     * the time at which the response was cached
     */
    'cache_time_header_name' => 'X-Cached-On',

    /*
     * Here you may define the cache store that should be used to store
     * requests. This can be the name of any store that is
     * configured in app/config/cache.php
     */
    'cache_store' => config('cache.default'),

    /*
     * Here you may define replacers that dynamically replace content from the response.
     * Each replacer must implement the Replacer interface.
     */
    'replacers' => [],

    /*
     * If the cache driver you configured supports tags, you may specify a tag name
     * here. All responses will be tagged. When clearing the responsecache only
     * items with that tag will be flushed.
     *
     * You may use a string or an array here.
     */
    'cache_tag' => '',

    /*
     * This class is responsible for generating a hash for a request. This hash
     * is used to look up an cached response.
     */
    'hasher' => DefaultHasher::class,

    /*
     * This class is responsible for serializing responses.
     */
    'serializer' => DefaultSerializer::class,
];
