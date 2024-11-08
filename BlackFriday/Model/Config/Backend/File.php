<?php

namespace Hiperdino\BlackFriday\Model\Config\Backend;

class File extends \Magento\Config\Model\Config\Backend\File
{
    
    private $_importHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Hiperdino\BlackFriday\Helper\Import $importHelper,
        array $data = []
    ) {
        $this->_uploaderFactory = $uploaderFactory;
        $this->_requestData = $requestData;
        $this->_filesystem = $filesystem;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_importHelper = $importHelper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $uploaderFactory, $requestData, $filesystem, $resource, $resourceCollection, $data);
    }

    /**
     * Save uploaded file before saving config value
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $file = $this->getFileData();
        if (!empty($file)) {
            $uploadDir = $this->_getUploadDir();
            try {
                /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
                $uploader = $this->_uploaderFactory->create(['fileId' => $file]);
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                $result = $uploader->save($uploadDir);
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('%1', $e->getMessage()));
            }

            $filename = $result['file'];
            if ($filename) {
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }
                $this->setValue($filename);

                // Llamar al método de rellenado de tablas pasándole la ruta al archivo CSV
                $fullFilePath = $result['path'] . '/' . $result['name'];
                try {

                    $this->_importHelper->importDataFromCsv($fullFilePath);

                } catch(\Exception $e) {
                    @unlink($fullFilePath); // Borrar archivo antes de finalizar
                    throw new \Magento\Framework\Exception\LocalizedException(__('%1', $e->getMessage()));
                }

                // Una vez finalizado el proceso borrar el archivo
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

        // Al final nunca guardamos ningún valor. Usamos este model solo para realizar la carga de datos
        // desde el archivo CSV a su correspondiente tabla.
        $this->unsValue();

        return $this;
    }
}
