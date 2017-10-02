<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace Magenerds\SystemDiff\Controller\Adminhtml\SystemDiff;

use Magenerds\SystemDiff\Service\PerformSystemDiffService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Diff extends Action
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var PerformSystemDiffService
     */
    private $performSystemDiffService;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * Diff action constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param PerformSystemDiffService $performSystemDiffService
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        PerformSystemDiffService $performSystemDiffService,
        TimezoneInterface $timezone
    ) {
        $this->context = $context;
        $this->jsonFactory = $jsonFactory;
        $this->performSystemDiffService = $performSystemDiffService;
        $this->timezone = $timezone;

        parent::__construct($context);
    }

    /**
     * Perform system diff.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {

        $result = $this->jsonFactory->create();

        $message = 'Diff successfully done at %s.';

        try {
            $this->performSystemDiffService->performDiff();
        } catch (\Exception $e) {
            $message = __("Error performing system diff at %s.");
            $result->setStatusHeader(500);
        }

        $result->setData(
            [
                'message' => sprintf(
                    __($message),
                    $this->timezone->formatDateTime(null, \IntlDateFormatter::SHORT, true))
            ]
        );

        return $result;
    }
}