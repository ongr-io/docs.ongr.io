<?php

namespace AppBundle\Service;

use Github\Client;
use Github\HttpClient\Cache\FilesystemCache;
use Github\HttpClient\CachedHttpClient;

class GithubParser
{
    private $client;

    /**
     * @var \Github\HttpClient\CachedHttpClient
     */
    private $cache;

    /**
     * @var string
     */
    private $githubToken = '';

    public function __construct($cacheDir = false, $file = false)
    {
        if ($cacheDir) $this->cache = new CachedHttpClient(array('cache_dir' => $cacheDir));
        if ($file) {
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
        if (!$this->client) {
            $this->client = new Client($this->cache);
            $this->client->authenticate($this->getGithubToken(), null, Client::AUTH_HTTP_TOKEN);
        }

        return $this->client;
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

    /**
     * @return string
     */
    public function getGithubToken()
    {
        return $this->githubToken;
    }

    /**
     * @param string $githubToken
     */
    public function setGithubToken($githubToken)
    {
        $this->githubToken = $githubToken;
    }

    /**
     * Scans Github repository directory contents.
     *
     * @param $org
     * @param $repo
     * @param $path
     * @param $reference
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getDirectoryContent($org, $repo, $path, $reference = []) {
        try {
            $dir = $this->getClient()->api('repo')->contents()->show($org, $repo, $path, $reference);
        } catch (\Exception $e) {
            $dir = [];
        }

        return $dir;
    }

    public function getReadme($org, $repo)
    {
        try {
            $readme = $this->getClient()->api('repo')->readme($org, $repo);
        } catch (\Exception $e) {
            $readme = '';
        }

        return $readme;
    }
}
