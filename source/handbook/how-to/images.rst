Uploading images to S3
======================

Step by step guide for uploading images to aws and displaying them with `Liip/Imagine` library.

Step 1. Getting required libraries.
-----------------------------------

.. code-block:: bash

    composer require aws/aws-sdk-php:2.* liip/imagine-bundle:~1.2 knplabs/knp-gaufrette-bundle:*@dev

..

Step 2. Add new bundles to AppKernel.
-------------------------------------

.. code-block:: php

    new Liip\ImagineBundle\LiipImagineBundle(),
    new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),

..

Step 3. Writing uploader class.
-------------------------------

There is simple example just for uploading.

.. code-block:: php

    <?php

    namespace Example\Class\Upload;

    use Aws\S3\S3Client;
    use Guzzle\Service\Resource\Model;

    /**
     * Uploads images to S3 bucket.
     */
    class S3Uploader
    {
        /**
         * @var S3Client
         */
        private $client;

        /**
         * @var string
         */
        private $bucket;

        /**
         * @param S3Client $client
         * @param string   $bucket
         */
        public function __construct(S3Client $client, $bucket)
        {
            $this->setClient($client);
            $this->setBucket($bucket);
        }

        /**
         * Upload file to S3.
         *
         * @param string $path
         * @param string $key
         * @param bool   $public
         */
        public function upload($path, $key, $public)
        {
            /** @var Model $response */
            $this->getClient()->putObject(
                [
                    'ACL' => $public ? 'public-read' : 'private',
                    'Bucket' => $this->getBucket(),
                    'Key' => $key,
                    'SourceFile' => $path,
                ]
            );
        }

        /**
         * @return string
         */
        public function getBucket()
        {
            return $this->bucket;
        }

        /**
         * @param string $bucket
         */
        public function setBucket($bucket)
        {
            $this->bucket = $bucket;
        }

        /**
         * @return S3Client
         */
        public function getClient()
        {
            return $this->client;
        }

        /**
         * @param S3Client $client
         */
        public function setClient($client)
        {
            $this->client = $client;
        }
    }

..

Step 4. Setting up services.
----------------------------

.. code-block:: yaml

    parameters:

        your_project.amazon.s3.client.config.aws_access_key_id : aws_access_key_id
        your_project.amazon.s3.client.config.aws_secret_key : aws_secret_key
        your_project.amazon.s3.client.config.bucket : bucketName

        your_project.amazon.s3.client.class : Aws\S3\S3Client
        your_project.imagine.cache.resolver.s3.class : Liip\ImagineBundle\Imagine\Cache\Resolver\AwsS3Resolver
        your_project.amazon.s3.uploader.class : Example\Class\Upload\S3Uploader

    services:

        your_project.amazon.s3.client:
            class: %your_project.amazon.s3.client.class%
            factory: [%your_project.amazon.s3.client.class%, factory]
            arguments :
                - key: %your_project.amazon.s3.client.config.aws_access_key_id%
                  secret : %your_project.amazon.s3.client.config.aws_secret_key%

        your_project.amazon.s3.uploader:
            class: %your_project.amazon.s3.uploader.class%
            arguments :
                - @your_project.amazon.s3.client
                - %your_project.amazon.s3.client.config.bucket%

        your_project.imagine.cache.resolver.s3:
            class : %your_project.imagine.cache.resolver.s3.class%
            arguments :
                - @your_project.amazon.s3.client
                - %your_project.amazon.s3.client.config.bucket%
                - "public-read"
                - { Scheme: https }
                - { CacheControl: 'max-age=86400' }
            tags :
                - { name: 'liip_imagine.cache.resolver', resolver: 'images' }

        your_project.imagine.loader.s3:
            class: %liip_imagine.binary.loader.stream.class%
            arguments:
                - 'gaufrette://amazon_s3/'
            tags:
                - name:   liip_imagine.binary.loader
                  loader: stream.amazon_s3


    _liip_imagine:
        resource: "@LiipImagineBundle/Resources/config/routing.xml"


    liip_imagine :
        cache: images
        data_loader : stream.amazon_s3

        loaders :
            stream.amazon_s3 :
                stream :
                    wrapper : gaufrette://amazon_s3/

        filter_sets:
            thumb_crop:
                filters:
                    thumbnail:
                        size: [32, 32]
                        mode: outbound # Transforms 50x40 to 32x32, while cropping the width
            thumb_resize:
                filters:
                    thumbnail:
                        size: [32, 32]
                        mode: inset # Transforms 50x40 to 32x26, no cropping

    knp_gaufrette:
        stream_wrapper: ~
        adapters:
            amazon_s3:
                aws_s3:
                    service_id: your_project.amazon.s3.client
                    bucket_name: %your_project.amazon.s3.client.config.bucket%
                    options:
                        create: true

        filesystems:
            amazon_s3:
                adapter: amazon_s3
..

Step 5. Uploading and displaying.
---------------------------------

Uploading:

.. code-block:: php

    /** @var S3Uploader $uploader */
    $uploader = $this->container->get('your_project.amazon.s3.uploader');

    $uploader->upload('path/to/image.php', 'example.png', true);

..

Getting cropped image:

.. code-block:: php

    /** @var DataManager $dataManager */
    $dataManager = $this->container->get('liip_imagine.data.manager');

    /** @var BinaryInterface $smt */
    $thumb = $dataManager->find('thumb_crop', 'example.png');
..

Additional reading.
-------------------

Liip/Imagine `documentation <http://symfony.com/doc/master/bundles/LiipImagineBundle/configuration.html>`_.

KnpLabs/Gaufrette `readme <https://github.com/KnpLabs/Gaufrette/blob/master/README.markdown>`_.

