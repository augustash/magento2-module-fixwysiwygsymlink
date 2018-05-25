<?php

/**
 * TODO: temporary override until patch or Magento 2.3 is released.
 * https://github.com/magento/magento2/issues/13929
 */
namespace Augustash\FixWysiwygSymlink\Override\App\Filesystem;

use Magento\Framework\App\Filesystem\DirectoryList;
use Augustash\Logger\Helper\Data as AshLogger;
use Augustash\FixWysiwygSymlink\Helper\Data as FixWysiwygSymlinkHelper;

/**
 * Magento directories resolver.
 */
class DirectoryResolver extends \Magento\Framework\App\Filesystem\DirectoryResolver
{
    /**
     * @var FixWysiwygSymlinkHelper
     */
    protected $helper;

    /**
     * @var AshLogger
     */
    protected $logger;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @param DirectoryList $directoryList
     * @param FixWysiwygSymlinkHelper $helper
     * @param AshLogger $logger
     */
    public function __construct(
        DirectoryList $directoryList,
        FixWysiwygSymlinkHelper $helper,
        AshLogger $logger
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->directoryList = $directoryList;

        parent::__construct($directoryList);
    }

    /**
     * Validate path.
     *
     * Gets real path for directory provided in parameters and compares it with specified root directory.
     * Will return TRUE if real path of provided value contains root directory path and FALSE if not.
     * Throws the \Magento\Framework\Exception\FileSystemException in case when directory path is absent
     * in Directories configuration.
     *
     * @see https://github.com/magento/magento2/issues/13929#issuecomment-371178960
     *
     * @param string $path
     * @param string $directoryConfig
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function validatePath($path, $directoryConfig = DirectoryList::MEDIA)
    {
        if (!$this->helper->isEnabled()) {
            return parent::validatePath($path, $directoryConfig);
        }

        $realPath = realpath($path);

        if ($this->helper->getAllowSymlinks()) {
            // BEGIN EDIT by @erikhansen
            /**
             * Since media directory is a symlink, need to run both paths through
             * realpath in order for the comparison to work.
             *
             * The proper fix for this should involve a STORE > Configuration
             * setting where an admin can choose whether to allow symlinked
             * directories.
             */
            $root = realpath($this->directoryList->getPath($directoryConfig));
            // END EDIT
        } else {
            $root = $this->directoryList->getPath($directoryConfig);
        }

        return strpos($realPath, $root) === 0;
    }
}
