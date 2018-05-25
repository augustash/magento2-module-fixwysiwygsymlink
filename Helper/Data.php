<?php

namespace Augustash\FixWysiwygSymlink\Helper;

use Augustash\Logger\Helper\Data as AshLogger;
use Magento\Framework\Exception\LocalizedException;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'augustash_fixwysiwygsymlink/general/enabled';
    const XML_PATH_ALLOW_SYMLINKS = 'augustash_fixwysiwygsymlink/general/allow_symlinks';

    /**
     * @var \Augustash\Logger\Helper\Data
     */
    protected $logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
         \Magento\Framework\App\Helper\Context $context,
         AshLogger $logger,
         \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param  null|integer  $storeId   # Magento store ID
     * @return boolean
     */
    public function isEnabled($storeId = null)
    {
        return (bool)$this->getConfig(self::XML_PATH_ENABLED, $storeId);
    }

    /**
     * @param  null|integer  $storeId   # Magento store ID
     * @return boolean
     */
    public function getAllowSymlinks($storeId = null)
    {
        return (bool)$this->getConfig(self::XML_PATH_ALLOW_SYMLINKS, $storeId);
    }

    /**
     * Returns the ID of the current store
     *
     * @return int
     */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getStoreId();
    }

    /**
     * Utility function to ease fetching of values from the Stores > Configuration
     *
     * @param  string           $field      # see the etc/adminhtml/system.xml for field names
     * @param  null|integer     $storeId    # Magento store ID
     * @return mixed
     */
    protected function getConfig($field, $storeId = null)
    {
        $storeId = (!is_null($storeId)) ? $storeId : $this->getCurrentStoreId();
        return $this->scopeConfig->getValue(
            $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

}
