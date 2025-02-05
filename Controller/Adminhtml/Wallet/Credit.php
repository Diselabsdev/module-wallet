<?php
namespace Diselabs\Wallet\Controller\Adminhtml\Wallet;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Diselabs\Wallet\Model\WalletFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\FloatValidator;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Data\Form\FormKey\Validator;

class Credit extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Diselabs_Wallet::manage';

    protected $resultJsonFactory;
    protected $walletFactory;
    protected $floatValidator;
    protected $_formKeyValidator;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        WalletFactory $walletFactory,
        FloatValidator $floatValidator,
        Validator $formKeyValidator
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->walletFactory = $walletFactory;
        $this->floatValidator = $floatValidator;
        $this->_formKeyValidator = $formKeyValidator;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        
        try {
            // Validate request method
            if (!$this->getRequest()->isPost()) {
                throw new LocalizedException(__('Invalid request method.'));
            }

            // Validate form key
            if (!$this->_formKeyValidator->validate($this->getRequest())) {
                throw new LocalizedException(__('Invalid form key. Please refresh the page.'));
            }

            $customerId = (int)$this->getRequest()->getParam('customer_id');
            $amount = (float)$this->getRequest()->getParam('amount');
            $description = $this->getRequest()->getParam('description');

            // Validate customer ID
            if ($customerId <= 0) {
                throw new LocalizedException(__('Invalid customer ID.'));
            }

            // Validate amount
            if (!$this->floatValidator->isValid($amount) || $amount <= 0) {
                throw new LocalizedException(__('Invalid amount.'));
            }

            // Validate description
            if (empty($description)) {
                throw new LocalizedException(__('Description is required.'));
            }

            // Get configuration values
            $minAmount = (float)$this->_scopeConfig->getValue('wallet/general/min_amount');
            $maxAmount = (float)$this->_scopeConfig->getValue('wallet/general/max_amount');

            // Validate amount against configuration
            if ($amount < $minAmount || ($maxAmount > 0 && $amount > $maxAmount)) {
                throw new LocalizedException(
                    __('Amount must be between %1 and %2.', $minAmount, $maxAmount)
                );
            }

            $wallet = $this->walletFactory->create()->load($customerId, 'customer_id');
            if (!$wallet->getId()) {
                $wallet->setCustomerId($customerId)
                    ->setBalance(0)
                    ->save();
            }

            // Add transaction logging
            $wallet->credit($amount, $description);

            return $result->setData([
                'success' => true,
                'message' => __('Amount credited successfully.')
            ]);
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Diselabs_Wallet::manage');
    }
}
