<?php
namespace Diselabs\Wallet\Model\ResourceModel\Wallet;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Diselabs\Wallet\Model\Wallet::class,
            \Diselabs\Wallet\Model\ResourceModel\Wallet::class
        );
    }
}
