<?php
namespace Diselabs\Wallet\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Wallet extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('diselabs_wallet_account', 'entity_id');
    }
}
