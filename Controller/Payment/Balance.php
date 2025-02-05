<?php
namespace Diselabs\Wallet\Controller\Payment;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session;
use Diselabs\Wallet\Model\WalletFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;

class Balance implements HttpGetActionInterface, CsrfAwareActionInterface
{
    protected $resultJsonFactory;
    protected $customerSession;
    protected $walletFactory;
    protected $request;

    public function __construct(
        JsonFactory $resultJsonFactory,
        Session $customerSession,
        WalletFactory $walletFactory,
        RequestInterface $request
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->walletFactory = $walletFactory;
        $this->request = $request;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return new InvalidRequestException($this->resultJsonFactory->create()->setData([
            'success' => false,
            'message' => __('Invalid Form Key. Please refresh the page.')
        ]));
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        
        try {
            if (!$this->customerSession->isLoggedIn()) {
                throw new LocalizedException(__('Customer not logged in'));
            }

            // Validate request method
            if (!$this->request->isGet()) {
                throw new LocalizedException(__('Invalid request method'));
            }

            $customerId = $this->customerSession->getCustomerId();
            
            // Additional security check to ensure the customer can only access their own wallet
            $wallet = $this->walletFactory->create()->load($customerId, 'customer_id');
            if (!$wallet->getId() || $wallet->getCustomerId() != $customerId) {
                throw new LocalizedException(__('Access denied'));
            }
            
            return $result->setData([
                'success' => true,
                'balance' => $wallet->getBalance() ?: 0
            ]);
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage(),
                'balance' => 0
            ]);
        }
    }
}
