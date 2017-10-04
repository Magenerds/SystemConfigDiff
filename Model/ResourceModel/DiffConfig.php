<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace Magenerds\SystemDiff\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class DiffConfig
 * @package Magenerds\SystemDiff\Model\ResourceModel
 */
class DiffConfig extends AbstractDb
{
    /**
     * Holds database field names
     */
    const LOCAL_VALUE_FIELD_NAME = 'diff_value_local';
    const REMOTE_VALUE_FIELD_NAME = 'diff_value_remote';
    const SCOPE_FIELD_NAME = 'scope';
    const SCOPE_ID_FIELD_NAME = 'scope_id';
    const PATH_FIELD_NAME = 'path';

    /**
     * Values for scope
     */
    const SCOPE_VALUE_WEBSITES = 'websites';
    const SCOPE_VALUE_STORES = 'stores';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenerds_systemdiff_diff_config', 'diff_value_id');
    }

    /**
     * Clear the actual config data
     */
    public function clearConfigData()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }
}