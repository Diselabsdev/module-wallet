<?php
namespace Diselabs\Wallet\Model;

use Magento\Framework\Model\AbstractModel;

class Wallet extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Diselabs\Wallet\Model\ResourceModel\Wallet::class);
    }

    public function credit($amount, $description = '')
    {
        if ($amount <= 0) {
            throw new \Exception(__('Credit amount must be greater than zero.'));
        }

        $this->setBalance($this->getBalance() + $amount);
        $this->save();

        // Create transaction record
        $transaction = $this->_transactionFactory->create();
        $transaction->setWalletId($this->getId())
            ->setType('credit')
            ->setAmount($amount)
            ->setDescription($description)
            ->save();

        return $this;
    }

    public function debit($amount, $description = '', $orderId = null)
    {
        if ($amount <= 0) {
            throw new \Exception(__('Debit amount must be greater than zero.'));
        }

        if ($this->getBalance() < $amount) {
            throw new \Exception(__('Insufficient wallet balance.'));
        }

        $this->setBalance($this->getBalance() - $amount);
        $this->save();

        // Create transaction record
        $transaction = $this->_transactionFactory->create();
        $transaction->setWalletId($this->getId())
            ->setType('debit')
            ->setAmount($amount)
            ->setDescription($description)
            ->setOrderId($orderId)
            ->save();

        return $this;
    }
}
