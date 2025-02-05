<?php
namespace Diselabs\Wallet\Controller\Adminhtml\Transaction;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Export extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Diselabs_Wallet::export';

    protected $fileFactory;
    protected $filesystem;
    protected $transactionCollection;
    protected $directory;

    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        Filesystem $filesystem,
        \Diselabs\Wallet\Model\ResourceModel\Transaction\CollectionFactory $transactionCollection
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
        $this->transactionCollection = $transactionCollection;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    public function execute()
    {
        try {
            // Validate request method
            if (!$this->getRequest()->isGet()) {
                throw new LocalizedException(__('Invalid request method.'));
            }

            $fileName = 'wallet_transactions_' . date('Y-m-d_H-i-s') . '.csv';
            $filePath = 'export/' . $fileName;

            // Ensure export directory exists and is writable
            $exportDir = 'export';
            if (!$this->directory->isExist($exportDir)) {
                $this->directory->create($exportDir);
            }

            if (!$this->directory->isWritable($exportDir)) {
                throw new LocalizedException(
                    __('Export directory is not writable.')
                );
            }

            // Create and secure the file
            $stream = $this->directory->openFile($filePath, 'w+');
            $stream->lock();

            try {
                $headers = [
                    'Transaction ID',
                    'Customer ID',
                    'Type',
                    'Amount',
                    'Description',
                    'Order ID',
                    'Created At'
                ];
                $stream->writeCsv($this->sanitizeRow($headers));

                $collection = $this->transactionCollection->create();
                $collection->addOrder('transaction_id', 'DESC');

                foreach ($collection as $transaction) {
                    $data = [
                        $transaction->getTransactionId(),
                        $transaction->getCustomerId(),
                        $transaction->getType(),
                        $transaction->getAmount(),
                        $this->sanitizeField($transaction->getDescription()),
                        $transaction->getOrderId(),
                        $transaction->getCreatedAt()
                    ];
                    $stream->writeCsv($this->sanitizeRow($data));
                }
            } finally {
                $stream->unlock();
                $stream->close();
            }

            return $this->fileFactory->create(
                $fileName,
                [
                    'type' => 'filename',
                    'value' => $filePath,
                    'rm' => true
                ],
                DirectoryList::VAR_DIR,
                'text/csv'
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->_redirect('*/*/');
        }
    }

    /**
     * Sanitize a row of data
     *
     * @param array $row
     * @return array
     */
    private function sanitizeRow($row)
    {
        return array_map([$this, 'sanitizeField'], $row);
    }

    /**
     * Sanitize a field value
     *
     * @param string $field
     * @return string
     */
    private function sanitizeField($field)
    {
        // Remove any potentially harmful characters
        $field = preg_replace('/[\x00-\x1F\x7F]/', '', (string)$field);
        // Prevent CSV injection
        if (in_array(substr($field, 0, 1), ['=', '+', '-', '@'])) {
            $field = "'" . $field;
        }
        return $field;
    }
}
