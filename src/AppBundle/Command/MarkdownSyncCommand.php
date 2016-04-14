<?php

namespace AppBundle\Command;

use AppBundle\Document\Content;
use AppBundle\Document\Paragraph;
use AppBundle\Service\GithubParser;
use AppBundle\Service\MarkDownParser;
use ONGR\ElasticsearchDSL\Query\MatchQuery;
use ONGR\ElasticsearchDSL\Query\MissingQuery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MarkdownSyncCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ongr:md:sync')
            ->setDescription('Sync docs from GitHub repos');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Sync docs from GitHub repos.');

        /** @var MarkDownParser $parser */
        $parser = $this->getContainer()->get('app.markdown.parser');

        /** @var GithubParser $github */
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

            $search = $contentRepo->createSearch()->addQuery($repoTermQuery)->setScroll();
            $results = $contentRepo->execute($search);

            foreach ($results as $document) {
                $manager->remove($document);
            }

            $manager->commit();
            $manager->refresh();

            try {
                $content = new Content();

                $readme = $github->getReadme($repo['org'], $repo['repo']);
                $this->parseSearchData($content, $readme);
                $content->setOrg($repo['org']);
                $content->setRepo($repo['repo']);
                $content->setBundle($repo['repo']);
                $content->setPath('README.md');
                $content->setTitle($content->getTitle() ?: $repo['repo']);
                $content->setContent($parser->parse(base64_decode($readme['content'])));
                $content->setUrl('/'.$repo['repo']);
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
                $file = $github->getClient()->api('repo')->contents()->show(
                    $repo['org'],
                    $repo['repo'],
                    $resource['path']
                );

                $content = new Content();
                $this->parseSearchData($content, $file);
                $content->setOrg($repo['org']);
                $content->setRepo($repo['repo']);
                $content->setBundle($repo['repo']);
                $content->setPath($path);
                $content->setTitle($content->getTitle() ?: explode('.', $resource['name'])[0]);
                $content->setMenuTitle(explode('.', $resource['name'])[0]);
                $content->setContent($parser->parse(base64_decode($file['content'])));
                $content->setUrl('/'.$repo['repo'] . '/' . $path);
                $content->setSha($resource['sha']);
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

        foreach ($results as $document) {
            $manager->remove($document);
        }

        $manager->commit();
        $manager->refresh();

        $io->block("Starting sync single pages.");
        $io->progressStart();

        foreach ($commonPages as $repo) {
            $file = $github->getClient()->api('repo')->contents()->show($repo['org'], $repo['repo'], $repo['path']);

            $content = new Content();
            $this->parseSearchData($content, $file);
            $content->setOrg($repo['org']);
            $content->setRepo($repo['repo']);
            $content->setPath($repo['path']);
            $content->setTitle($content->getTitle() ?: $repo['title']);
            $content->setContent($parser->parse(base64_decode($file['content'])));
            $content->setUrl('/common/'.explode('.', $repo['path'])[0]);
            $content->setSha($file['sha']);
            $manager->persist($content);
            $manager->commit();

            $io->progressAdvance();
        }
        $io->progressFinish();

        $io->success("Finished sync with GitHub");
    }

    /**
     * Scans directory recursively
     *
     * @param string              $name
     * @param string              $org
     * @param string              $repo
     * @param string              $path
     * @param StyleInterface|null $io
     *
     * @return array
     */
    private function scanDirectory($name, $org, $repo, $path, StyleInterface $io = null)
    {
        $docs = [];
        $github = $this->getContainer()->get('app.github.parser');
        $dir = $github->getDirectoryContent($org, $repo, $path);

        foreach ($dir as $resource) {
            switch ($resource['type']) {
                case 'dir':
                    $docs = array_merge(
                        $docs,
                        $this->scanDirectory($name.$resource['name'].'/', $org, $repo, $resource['path'])
                    );
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

    /**
     * Parses headings ana paragraphs used for search only
     *
     * @param Content $content
     * @param string  $file
     */
    private function parseSearchData(Content &$content, $file)
    {
        /** @var MarkDownParser $parser */
        $parser = $this->getContainer()->get('app.markdown.parser');
        $blocks = $parser->parseBlocks(base64_decode($file['content']));

        foreach ($blocks as $key => $block) {
            if ($block[0] == 'headline') {
                $content->addHeadline(new Paragraph($parser->renderAbsyText([$block]), $block['level']));
                if (!$content->getTitle() && $block['level'] == 1 && $block['content'][0][0] == 'text') {
                    $content->setTitle($parser->renderAbsyText([$block]));
                }
            } else {
                $absyText = $parser->renderAbsyText([$block]);
                $content->addParagraph(new Paragraph($absyText, 1));
                $content->setDescription(
                    $content->getDescription() . ($block[0] == 'code' ? '[code] ' : $absyText . ' ')
                );
            }
        }

        $content->setDescription(rtrim(substr($content->getDescription(), 0, 245), " \t\n\r\0\x0B.") . '...');
        $content->setTitle(
            !$content->getTitle()
                ? (isset($content->getHeadlines()[0]) ? $content->getHeadlines()[0]->getValue() : null)
                : $content->getTitle()
        );
    }
}
