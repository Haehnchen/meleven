<?php

namespace Shopware\SmMeleven\Media;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Util;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Media\Media;
use Shopware\SmMeleven\Exporter\AliasFinder;
use Shopware\SmMeleven\Exporter\Exception\MediaExportException;
use Shopware\SmMeleven\Exporter\ImageExporter;
use Shopware\SmMeleven\Struct\MelevenConfig;
use Shopware\SmMeleven\Utils\MelevenUtil;

/**
 * Class MediaAdapter
 * @package Shopware\SmMeleven
 */
class MediaAdapter implements AdapterInterface
{

    /**
     * @var ImageExporter
     */
    private $imageExporter;

    /**
     * @var MelevenConfig
     */
    private $config;

    /**
     * @var AliasFinder
     */
    private $finder;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * MediaAdapter constructor.
     * @param ImageExporter $imageExporter
     * @param AliasFinder $finder
     * @param MelevenConfig $config
     * @param ModelManager $modelManager
     */
    public function __construct(
        ImageExporter $imageExporter,
        AliasFinder $finder,
        MelevenConfig $config,
        ModelManager $modelManager
    ) {
        $this->imageExporter = $imageExporter;
        $this->finder = $finder;
        $this->config = $config;
        $this->modelManager = $modelManager;
    }

    /**
     * {@inheritdoc}
     */
    public function write($path, $contents, Config $config)
    {
        $tmpname = basename(MelevenUtil::normalizePath($path));

        $filename = sys_get_temp_dir() . '/' . $tmpname;

        $stream = fopen($filename, 'w+b');
        fwrite($stream, $contents);
        rewind($stream);
        $result = $this->writeStream($path, $stream, $config);
        fclose($stream);
        unlink($filename);

        if ($result === false) {
            return false;
        }

        $result['contents'] = $contents;
        $result['mimetype'] = Util::guessMimeType($path, $contents);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, Config $config)
    {
        // already external file
        if (MelevenUtil::isDerivativesPath($path)) {
            return [
                'path' => $path,
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
            ];
        }

        try {
            $melevenPath = $this->imageExporter->exportMedia(
                $this->config, MelevenUtil::normalizePath($path), $resource, $config
            );

            /** @var Media $media */
            $media = $this->modelManager->getRepository(Media::class)
                ->findOneBy(['path' => $path]);

            if (null !== $media) {
                $media->setAttribute(['meleven_id' => $melevenPath]);
                $this->modelManager->flush($media);
            }
        } catch (MediaExportException $e) {
            return false;
        }

        return [
            'path' => $melevenPath,
            'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $resource, Config $config)
    {
        throw new \RuntimeException(sprintf('updateStream not implemented for "%s"', $path));
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newpath)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path, $newpath)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($dirname)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($dirname, Config $config)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setVisibility($path, $visibility)
    {
        return compact('visibility');
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        return strpos($path, 'out') === 0 && $this->finder->hasMelevenId(basename($path));
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        return [
            'contents' => file_get_contents('http://api.meleven.de/' . $path),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        throw new \RuntimeException(sprintf('readStream not implemented for "%s"', $path));
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false)
    {
        throw new \RuntimeException(sprintf('listContents not implemented for "%s"', $directory));
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($path)
    {
        throw new \RuntimeException(sprintf('getMetadata not implemented for "%s"', $path));
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path)
    {
        throw new \RuntimeException(sprintf('getSize not implemented for "%s"', $path));
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path)
    {
        throw new \RuntimeException(sprintf('getMimetype not implemented for "%s"', $path));
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp($path)
    {
        throw new \RuntimeException(sprintf('getTimestamp not implemented for "%s"', $path));
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibility($path)
    {
        return AdapterInterface::VISIBILITY_PUBLIC;
    }

}
