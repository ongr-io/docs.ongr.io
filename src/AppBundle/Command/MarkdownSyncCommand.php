<?php

namespace AppBundle\Command;

use AppBundle\Document\Content;
use cebe\markdown\GithubMarkdown;
use Github\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MarkdownSyncCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ongr:md:sync')
            ->setDescription('Sync docs from Github repos')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = new GithubMarkdown();
        $parser->html5 = true;
//        $content = $this->getContainer()->get('es.manager.default.content');

        $github = $this->getContainer()->get('app.github.parser');
        $repos = $this->getContainer()->getParameter('repos');

        $manager = $this->getContainer()->get('es.manager.default');

        foreach ($repos as $repo) {

            try {
                $readme = $github->getReadme($repo['org'], $repo['repo']);
                $content = new Content();
                $content->bundle = $repo['repo'];
                $content->path = 'README.md';
                $content->title = $repo['repo'];
                $content->content = $parser->parse(base64_decode($readme['content']));
                $content->url = '/'.$repo['repo'];
                $manager->persist($content);
                $manager->commit();
            } catch (\Exception $e) {
                continue;
            }

            $resources = [
                'Resources/doc',
                'docs',
                'doc',
            ];

            $docs = [];
            foreach ($resources as $resource) {
                $docs = array_merge($docs, $this->scanDirectory('', $repo['org'], $repo['repo'], $resource));
            }

            foreach ($docs as $path => $resource) {
                $file = $github->getClient()->api('repo')->contents()->show($repo['org'], $repo['repo'], $resource['path']);

                $content = new Content();
                $content->bundle = $repo['repo'];
                $content->path = $path;
                $content->title = explode('.', $resource['name'])[0];
                $content->content = $parser->parse(base64_decode($file['content']));
                $content->url = '/'.$repo['repo'] . '/' . $path;
                $content->sha = $resource['sha'];
                $manager->persist($content);
                $manager->commit();
            }
        }

        $output->writeln('Ok, it\'s done!');
    }

    private function scanDirectory($name, $org, $repo, $path) {

        $docs = [];
        $github = $this->getContainer()->get('app.github.parser');

        $dir = $github->getDirectoryContent($org, $repo, $path);
        foreach ($dir as $resource) {
            switch ($resource['type']) {
                case 'dir':
                    $docs = array_merge($docs, $this->scanDirectory($name.$resource['name'].'/', $org, $repo, $resource['path']));
                    break;
                case 'file':
                    $key = $name . explode('.', $resource['name'])[0];
                    $docs[$key] = $resource;
                    break;
            }
        }

        return $docs;
    }
}