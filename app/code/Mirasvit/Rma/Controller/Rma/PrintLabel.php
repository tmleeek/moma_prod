<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rma
 * @version   1.1.22
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Controller\Rma;

use Magento\Framework\Controller\ResultFactory;

class PrintLabel extends \Mirasvit\Rma\Controller\Rma
{
    public function __construct(
        \Mirasvit\Rma\Helper\Controller\Rma\GuestStrategy $guestStrategy,
        \Mirasvit\Rma\Api\Service\Attachment\AttachmentManagementInterface $attachmentManagement,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Rma\Helper\Controller\Rma\StrategyFactory $strategyFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->guestStrategy        = $guestStrategy;
        $this->attachmentManagement = $attachmentManagement;
        $this->rmaManagement        = $rmaManagement;
        $this->registry             = $registry;

        parent::__construct($strategyFactory, $customerSession, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function isRequireCustomerAutorization()
    {
        return $this->strategy->isRequireCustomerAutorization();
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $rma = $this->guestStrategy->initRma($this->getRequest());
        if (!$rma) {
            return $resultRedirect->setPath('/');
        }

        if ($label = $this->rmaManagement->getReturnLabel($rma)) {
            return $resultRedirect->setPath($this->attachmentManagement->getUrl($label));
        } else {
            $this->_forward('no_rote');
        }
    }
}