<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace Magenerds\SystemDiff\Helper;

use DateTimeInterface;
use Magenerds\SystemDiff\Model\DiffConfig;
use Magenerds\SystemDiff\Model\ResourceModel\DiffConfig as DiffConfigResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magenerds\SystemDiff\Model\ResourceModel\DiffConfig\CollectionFactory;
use Magenerds\SystemDiff\Model\DiffConfigFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class to read module-specific configuration
 *
 * Class Config
 * @package Magenerds\SystemDiff\Helper
 */
class Config
{
    /**
     * Holds the configuration paths for this module
     */
    const XML_PATH_ENABLED = 'system_diff/general/enabled';
    const XML_PATH_REMOTE_SYSTEM_URL = 'system_diff/connection/remote_system_url';
    const XML_PATH_ACCESS_TOKEN = 'system_diff/connection/access_token';
    const XML_PATH_API_TYPE = 'system_diff/connection/api_type';
    const XML_PATH_DISPLAY_STORE_CONFIG = 'system_diff/display/store_config';
    const XML_PATH_LAST_SYNC_DATETIME = 'system_diff/general/last_sync_datetime';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CollectionFactory
     */
    private $diffConfigCollectionFactory;

    /**
     * @var DiffConfigFactory
     */
    private $diffConfigFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory    $collectionFactory
     * @param DiffConfigFactory    $diffConfigFactory
     * @param TimezoneInterface    $timezone
     * @param DateTime             $dateTime
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory,
        DiffConfigFactory $diffConfigFactory,
        TimezoneInterface $timezone,
        DateTime $dateTime
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->diffConfigCollectionFactory = $collectionFactory;
        $this->diffConfigFactory = $diffConfigFactory;
        $this->dateTime = $dateTime;
        $this->timezone = $timezone;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED);
    }

    /**
     * @return string|null
     */
    public function getRemoteSystemUrl()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_REMOTE_SYSTEM_URL);
    }

    /**
     * Returns the class/type name to use as Client.
     *
     * @return string|null
     */
    public function getApiType()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_TYPE);
    }

    /**
     * @return string|null
     */
    public function getRemoteSystemAccessToken()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ACCESS_TOKEN);
    }

    /**
     * @return bool
     */
    public function isDisplayStoreConfig()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DISPLAY_STORE_CONFIG);
    }

    /**
     * Return a string with date time of last sync or n/a.
     *
     * @return string
     */
    public function getLastSyncDatetimeFormatted()
    {
        $dateTime = $this->getLastSyncDatetime();
        if ($dateTime instanceof DateTimeInterface) {

            return $this->timezone->formatDateTime($dateTime, \IntlDateFormatter::SHORT, true);
        }

        return (string)__('n/a');
    }

    /**
     * The last saved sync datetime timestamp as string.
     *
     * Should be a gmt timestamp.
     *
     * @return DateTimeInterface|null;
     */
    public function getLastSyncDatetime()
    {
        $dataObject = $this->getLastSyncDiffConfig();
        $dateTime = null;
        if ($dataObject instanceof DiffConfig) {
            $timeStampString = $dataObject->getDiffValueLocal();
            $dateTime = \DateTime::createFromFormat("U", $timeStampString);
        }

        return $dateTime;
    }

    /**
     * DiffConfig entry that holds the last sync time.
     *
     * @return DiffConfig|null;
     */
    protected function getLastSyncDiffConfig()
    {
        $collection = $this->diffConfigCollectionFactory->create();

        /** @var DiffConfig $dataObject */
        $dataObject = $collection->getItemByColumnValue(
            DiffConfigResource::PATH_FIELD_NAME,
            self::XML_PATH_LAST_SYNC_DATETIME
        );

        return $dataObject;
    }

    /**
     * Create or update last sync entry.
     *
     * @return void
     */
    public function updateLastDiffTimestamp()
    {
        $diffConfig = $this->getLastSyncDiffConfig();
        if (!($diffConfig instanceof DiffConfig)) {
            $diffConfig = $this->diffConfigFactory->create();
            $diffConfig->setScope('default');
            $diffConfig->setPath(Config::XML_PATH_LAST_SYNC_DATETIME);
        }
        $diffConfig->setDiffValueLocal($this->dateTime->gmtTimestamp());
        $diffConfig->save();
    }
}