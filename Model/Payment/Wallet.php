<?php
namespace Diselabs\Wallet\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;
use Diselabs\Wallet\Model\WalletFactory;

class Wallet extends AbstractMethod
{
    protected $_code = 'wallet';
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_canVoid = true;
    protected $_canUseCheckout = true;
    protected $_canUseInternal = true;

    protected $walletFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        WalletFactory $walletFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            null,
            null,
            $data
        );
        $this->walletFactory = $walletFactory;
    }

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $customerId = $payment->getOrder()->getCustomerId();
        $wallet = $this->walletFactory->create()->load($customerId, 'customer_id');

        if (!$wallet->getId() || $wallet->getBalance() < $amount) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Insufficient wallet balance.')
            );
        }

        return $this;
    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $customerId = $payment->getOrder()->getCustomerId();
        $orderId = $payment->getOrder()->getId();
        $wallet = $this->walletFactory->create()->load($customerId, 'customer_id');

        try {
            $wallet->debit($amount, __('Payment for order #%1', $orderId), $orderId);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }

        return $this;
    }
}
