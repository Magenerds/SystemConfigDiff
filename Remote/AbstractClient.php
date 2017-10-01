<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace Magenerds\SystemDiff\Remote;

use Magenerds\SystemDiff\Helper\Config;
use Magenerds\SystemDiff\Api\Data\ConfigDataInterfaceFactory;

class AbstractClient
{
    /**
     * @var Config
     */
    protected $helper;

    /**
     * @var ConfigDataInterfaceFactory
     */
    protected $configDataFactory;

    /**
     * Client constructor.
     *
     * @param Config $configHelper
     * @param ConfigDataInterfaceFactory $configDataFactory
     */
    public function __construct(
        Config $configHelper,
        ConfigDataInterfaceFactory $configDataFactory
    ) {
        $this->helper = $configHelper;
        $this->configDataFactory = $configDataFactory;
    }

    /**
     * @param $json
     * @return []
     */
    protected function buildDataFromJson($json)
    {
        $responseData = json_decode($json, true);
        if (is_array($responseData)) {
            return $responseData;
        }

        return [];
    }
}