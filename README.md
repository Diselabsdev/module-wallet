# Diselabs Wallet Module for Magento 2

## Overview
The Diselabs Wallet module adds a comprehensive digital wallet system to your Magento 2 store. It allows customers to maintain a wallet balance, use it for purchases, and administrators to manage wallet transactions.

## Features
- Customer wallet management
- Secure wallet transactions
- Admin wallet management interface
- Wallet payment method in checkout
- Transaction history
- CSV export functionality
- Configurable minimum and maximum amounts
- Secure implementation with proper validation and sanitization

## Installation

### Requirements
- Magento 2.4.x
- PHP 7.4 or higher
- Composer 2.x

### Installation Methods

#### Method 1: Composer Installation (Recommended)
1. Add the repository to your Magento 2 `composer.json`:
   ```bash
   composer config repositories.diselabs-wallet vcs https://github.com/diselabs/module-wallet.git
   ```

2. Require the module:
   ```bash
   composer require diselabs/module-wallet:^1.0
   ```

3. Enable the module:
   ```bash
   php bin/magento module:enable Diselabs_Wallet
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento setup:static-content:deploy -f
   php bin/magento cache:clean
   ```

#### Method 2: Git Installation
1. Navigate to your Magento 2 root directory
2. Create the following directory structure:
   ```bash
   mkdir -p app/code/Diselabs/Wallet
   ```

3. Clone the repository:
   ```bash
   git clone https://github.com/diselabs/module-wallet.git app/code/Diselabs/Wallet
   ```

4. Enable the module:
   ```bash
   php bin/magento module:enable Diselabs_Wallet
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento setup:static-content:deploy -f
   php bin/magento cache:clean
   ```

#### Method 3: Manual Installation
1. Download the latest release from [GitHub Releases](https://github.com/diselabs/module-wallet/releases)
2. Extract the contents to `app/code/Diselabs/Wallet` in your Magento installation
3. Follow the same enable commands as above

## Configuration

### Admin Configuration
1. Go to `Stores > Configuration > Diselabs > Wallet Settings`
2. Configure the following settings:
   - Enable/Disable wallet system
   - Set minimum amount for credit
   - Set maximum amount for credit

### Permissions
1. Go to `System > Permissions > User Roles`
2. Configure the following permissions:
   - Wallet Management
   - Wallet Configuration
   - Export Transactions

## Usage

### Customer Features

#### Viewing Wallet Balance
1. Log in to customer account
2. Navigate to "My Account > My Wallet"
3. View current balance and transaction history

#### Using Wallet for Payment
1. Add products to cart
2. Proceed to checkout
3. Select "Pay with Wallet" as payment method
4. Complete the order if sufficient balance is available

### Admin Features

#### Crediting Customer Wallet
1. Go to `Customers > All Customers`
2. Select a customer
3. Click "Credit Wallet"
4. Enter amount and description
5. Submit to credit the wallet

#### Exporting Transactions
1. Go to `Sales > Wallet Transactions`
2. Click "Export CSV"
3. Download the transaction report

## Security Features
- CSRF protection
- Input validation
- Secure file handling
- ACL implementation
- Data sanitization
- Error handling
- Access control
- Transaction logging

## File Structure
```
Diselabs/Wallet/
├── Block/
│   └── Customer/
│       └── Wallet.php
├── Controller/
│   ├── Adminhtml/
│   │   ├── Transaction/
│   │   │   └── Export.php
│   │   └── Wallet/
│   │       └── Credit.php
│   └── Payment/
│       └── Balance.php
├── Model/
│   ├── ResourceModel/
│   │   └── Wallet/
│   │       └── Collection.php
│   └── Wallet.php
├── etc/
│   ├── acl.xml
│   ├── config.xml
│   ├── module.xml
│   ├── payment.xml
│   └── security.xml
└── view/
    ├── adminhtml/
    └── frontend/
        ├── layout/
        ├── templates/
        └── web/
            ├── js/
            └── template/
```

## Database Schema

### Wallet Account Table
```sql
diselabs_wallet_account
- entity_id (primary)
- customer_id (foreign key to customer_entity)
- balance
- created_at
- updated_at
```

### Transaction Table
```sql
diselabs_wallet_transaction
- transaction_id (primary)
- wallet_id (foreign key to wallet_account)
- type
- amount
- description
- order_id
- created_at
```

## Support
For support and issues, please contact:
- Email: support@diselabs.com
- Website: https://www.diselabs.com

## Contributing
1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License
This module is licensed under the MIT License - see the LICENSE file for details.

## Changelog
### Version 1.0.0
- Initial release
- Basic wallet functionality
- Admin management interface
- Customer wallet interface
- Payment method integration
- Transaction export functionality

## Security Considerations
- Always validate user input
- Use proper ACL checks
- Implement CSRF protection
- Sanitize data output
- Follow Magento security best practices

## Best Practices
1. Always use the wallet through provided interfaces
2. Regularly backup wallet data
3. Monitor transaction logs
4. Keep the module updated
5. Review security settings periodically

## Troubleshooting
### Common Issues
1. Wallet not showing in checkout
   - Check if module is enabled
   - Verify payment method configuration
   - Clear cache

2. Unable to credit wallet
   - Verify admin permissions
   - Check minimum/maximum amount settings
   - Verify customer exists

3. Export not working
   - Check directory permissions
   - Verify admin export permissions
   - Check file system permissions
