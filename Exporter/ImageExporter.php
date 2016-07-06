<?php

namespace SmMeleven\Exporter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use League\Flysystem\Config;
use Psr\Log\LoggerInterface;
use Shopware\Bundle\AttributeBundle\Service\DataPersister;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Media\Media;
use SmMeleven\Exporter\Exception\MediaExportException;
use SmMeleven\Struct\MelevenConfig;

class ImageExporter
{
    /**
     * @var Client
     */
    private $client;
    
    /**
     * @var LoggerInterface
     */
    private $logger;

    private $modelManager;

    public function __construct(Client $client, LoggerInterface $logger, ModelManager $modelManager)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->modelManager = $modelManager;
    }

    public function exportMedia(MelevenConfig $meleven, $path, $resource, Config $config)
    {
        try {
            $request = $this->client->post('http://api.meleven.de/image', [
                'body' => [
                    'image' => $resource,
                ],
                'auth' => [
                    $meleven->getUser(),
                    $meleven->getPassword()
                ]
            ]);
        } catch (RequestException $e) {
            throw new MediaExportException(sprintf('Invalid response for "%s"', $path), $e->getCode(), $e);
        }

        $content = json_decode((string)$request->getBody(), true);
        if (!isset($content[0]['id'])) {
            throw new MediaExportException(sprintf('Invalid response for "%s"', $path));
        }

        /** @var Media $media */
        $media = $this->modelManager->getRepository(Media::class)
            ->findOneBy(['path' => $path]);

        if ($media) {
            /** @var DataPersister $persister */
            $persister = Shopware()->Container()->get('shopware_attribute.data_persister');
            $persister->persist(['meleven_id' => $content[0]['id']], 's_media_attributes', $media->getId());
        }
        
        return sprintf('out/%s/%s', $meleven->getChannel(), $content[0]['id']);
    }
}