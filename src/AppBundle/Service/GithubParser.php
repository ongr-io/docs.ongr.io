<?php

namespace AppBundle\Service;

use Github\Client;
use Github\HttpClient\Cache\FilesystemCache;
use Github\HttpClient\CachedHttpClient;

class GithubParser
{
    /**
     * @var null|\Github\HttpClient\CachedHttpClient
     */
    protected $cache = null;

    public function __construct($cacheDir = false, $file = false)
    {
        if ($cacheDir) $this->cache = new CachedHttpClient(array('cache_dir' => $cacheDir));
        if ($file)
        {
            $this->cache = new CachedHttpClient(
                array(),
                null,
                new FilesystemCache($file)
            );
        }
    }

    /**
     * Get a client object, so you can access to all GitHub
     *
     * @return \Github\Client
     */
    public function getClient()
    {
        return new Client($this->cache);
    }

    /**
     * Get cache
     *
     * @return null|\Github\HttpClient\CachedHttpClient $cache
     */
    public function getCache()
    {
        return $this->cache;
    }
}
