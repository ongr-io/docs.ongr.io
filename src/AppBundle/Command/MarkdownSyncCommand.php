<?php

namespace AppBundle\Command;

use AppBundle\Document\Content;
use cebe\markdown\GithubMarkdown;
use Github\Client;
use ONGR\ElasticsearchDSL\Query\MatchQuery;
use ONGR\ElasticsearchDSL\Query\MissingQuery;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
        $io = new SymfonyStyle($input, $output);
        $io->title('Sync docs from Github repos.');

        $parser = $this->getContainer()->get('app.markdown.parser');

        $github = $this->getContainer()->get('app.github.parser');
        $repos = $this->getContainer()->getParameter('repos');

        $manager = $this->getContainer()->get('es.manager.default');
        $contentRepo = $this->getContainer()->get('es.manager.default.content');

        $io->createProgressBar();
        foreach ($repos as $repo) {

            $io->block("Starting sync with: ".$repo['org'].'/'.$repo['repo']);
            $io->progressStart();
            $parser->setBundle($repo['repo']);
            $repoTermQuery = new MatchQuery('bundle', $repo['repo']);
//            $orgTermQuery = new TermQuery('bundle', $repo['repo']);

            $search = $contentRepo->createSearch()->addQuery($repoTermQuery)->setScroll();
            $results = $contentRepo->execute($search);

            foreach ($results as $document)
            {
                $manager->remove($document);
            }
            $manager->commit();
            $manager->refresh();

            try {
                $readme = $github->getReadme($repo['org'], $repo['repo']);
                $content = new Content();
                $content->org = $repo['org'];
                $content->repo = $repo['repo'];
                $content->bundle = $repo['repo'];
                $content->path = 'README.md';
                $content->title = $repo['repo'];
                $content->content = $parser->parse(base64_decode($readme['content']));
                $content->url = '/'.$repo['repo'];
                $content->category = 'bundle-homepage';
                $manager->persist($content);
                $manager->commit();

                $io->progressAdvance();
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
                $docs = array_merge($docs, $this->scanDirectory('', $repo['org'], $repo['repo'], $resource, $io));
            }

            foreach ($docs as $path => $resource) {
                $file = $github->getClient()->api('repo')->contents()->show($repo['org'], $repo['repo'], $resource['path']);

                $content = new Content();
                $content->org = $repo['org'];
                $content->repo = $repo['repo'];
                $content->bundle = $repo['repo'];
                $content->path = $path;
                $content->title = explode('.', $resource['name'])[0];
                $content->content = $parser->parse(base64_decode($file['content']));
                $content->url = '/'.$repo['repo'] . '/' . $path;
                $content->sha = $resource['sha'];
                $manager->persist($content);
                $manager->commit();

                $io->progressAdvance();
            }
            $manager->refresh();
            $io->progressFinish();
        }

        $commonPages = $this->getContainer()->getParameter('commons');

        $repoMissing = new MissingQuery('bundle');
        $search = $contentRepo->createSearch()->addFilter($repoMissing)->setScroll();
        $results = $contentRepo->execute($search);

        foreach ($results as $document)
        {
            $manager->remove($document);
        }
        $manager->commit();
        $manager->refresh();

        $io->block("Starting sync single pages.");
        $io->progressStart();
        foreach ($commonPages as $repo) {

            $file = $github->getClient()->api('repo')->contents()->show($repo['org'], $repo['repo'], $repo['path']);

            $content = new Content();
            $content->org = $repo['org'];
            $content->repo = $repo['repo'];
            $content->path = $repo['path'];
            $content->title = $repo['title'];
            $content->content = $parser->parse(base64_decode($file['content']));
            $content->url = '/common/'.explode('.', $repo['path'])[0];
            $content->sha = $file['sha'];
            $manager->persist($content);
            $manager->commit();

            $io->progressAdvance();
        }
        $io->progressFinish();

        $io->success("Finished sync with Github");
    }

    private function scanDirectory($name, $org, $repo, $path, StyleInterface $io = null) {

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
            $io ? $io->progressAdvance() : null;
        }

        return $docs;
    }
}