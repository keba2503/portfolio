<?php

namespace Hiperdino\Dinitos\Model\Config\Backend;

use Exception;
use Hiperdino\Dinitos\Helper\Import;
use Hiperdino\Dinitos\Helper\Logger;
use Magento\Config\Model\Config\Backend\File as FileModel;
use Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;

class File extends FileModel
{
    protected Import $dinitosImport;
    protected Logger $logger;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        UploaderFactory $uploaderFactory,
        RequestDataInterface $requestData,
        Filesystem $filesystem,
        Import $import,
        Logger $logger,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->dinitosImport = $import;
        $this->logger = $logger;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_requestData = $requestData;
        $this->_filesystem = $filesystem;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context, $registry, $config, $cacheTypeList, $uploaderFactory, $requestData, $filesystem, $resource, $resourceCollection, $data);
    }

    /**
     * Save uploaded file before saving config value
     *
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $file = $this->getFileData();

        if (!empty($file)) {
            $uploadDir = $this->_getUploadDir();
            try {
                /** @var Uploader $uploader */
                $uploader = $this->_uploaderFactory->create(['fileId' => $file]);
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                $result = $uploader->save($uploadDir);
            } catch (Exception $e) {
                $this->logger->log("Error al procesar CSV: " . $e->getMessage());
                throw new LocalizedException(__('%1', $e->getMessage()));
            }

            $filename = $result['file'];
            if ($filename) {
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }

                $this->setValue($filename);

                $this->logger->log("Iniciando el proceso de importación desde el archivo: $filename");

                $fullFilePath = $result['path'] . '/' . $result['name'];

                try {
                    $this->dinitosImport->importDataFromCsv($fullFilePath);
                    $this->logger->log("El proceso de importación se completó con éxito.");
                } catch (Exception $e) {
                    @unlink($fullFilePath);
                    $this->logger->log("Error durante el proceso de importación: " . $e->getMessage());
                    throw new LocalizedException(__('%1', $e->getMessage()));
                }

                @unlink($fullFilePath);
            }
        } else {
            if (is_array($value) && !empty($value['delete'])) {
                $this->setValue('');
            } elseif (is_array($value) && !empty($value['value'])) {
                $this->setValue($value['value']);
            } else {
                $this->unsValue();
            }
        }

        $this->unsValue();

        return $this;
    }
}