<?php
namespace Diselabs\Wallet\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Diselabs\Wallet\Model\WalletFactory;
use Magento\Customer\Model\Session;

class Wallet extends Template
{
    protected $walletFactory;
    protected $customerSession;

    public function __construct(
        Context $context,
        WalletFactory $walletFactory,
        Session $customerSession,
        array $data = []
    ) {
        $this->walletFactory = $walletFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function getWalletBalance()
    {
        $customerId = $this->customerSession->getCustomerId();
        $wallet = $this->walletFactory->create()->load($customerId, 'customer_id');
        return $wallet->getBalance() ?: 0;
    }

    public function getTransactionHistory()
    {
        $customerId = $this->customerSession->getCustomerId();
        $wallet = $this->walletFactory->create()->load($customerId, 'customer_id');
        if ($wallet->getId()) {
            return $wallet->getTransactionCollection();
        }
        return [];
    }
}
