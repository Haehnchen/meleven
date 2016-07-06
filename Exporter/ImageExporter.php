<?php

namespace Shopware\SmMeleven\Exporter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use League\Flysystem\Config;
use Psr\Log\LoggerInterface;
use Shopware\CustomModels\MelevenImage;
use Shopware\CustomModels\MelevenImageRepository;
use Shopware\SmMeleven\Exporter\Exception\MediaExportException;
use Shopware\SmMeleven\Struct\MelevenConfig;

class ImageExporter
{
    /**
     * @var Client
     */
    private $client;
    
    /**
     * @var MelevenImageRepository
     */
    private $er;
    
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Client $client, MelevenImageRepository $er, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->er = $er;
        $this->logger = $logger;
    }

    public function exportMedia(MelevenConfig $meleven, $path, $resource, Config $config)
    {
        try {
            $request = $this->client->post('http://api.meleven.de/image', [
                'body' => [
                    'image' => $resource,
                ],
                'auth' => [$meleven->getUser(), $meleven->getPassword()],
            ]);
        } catch (RequestException $e) {
            throw new MediaExportException(sprintf('Invalid response for "%s"', $path), $e->getCode(), $e);
        }

        $content = json_decode((string) $request->getBody(), true);
        if(!isset($content[0]['id'])) {
            throw new MediaExportException(sprintf('Invalid response for "%s"', $path));
        }

        $basename = basename($path);
        
        if($id = $this->er->findMelevenIdByBasename($basename)) {
            return sprintf('out/%s/%s', $meleven->getChannel(), $id);
        }

        $this->er->createAndClear(
            new MelevenImage($basename, $path, $content[0]['id'], $content[0])
        );
        
        return sprintf('out/%s/%s', $meleven->getChannel(), $content[0]['id']);
    }
}