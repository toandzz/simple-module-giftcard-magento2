<?php
namespace Mageplaza\GiftCard\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {

        $installer = $setup;

        $installer->startSetup();


        if(version_compare($context->getVersion(), '4.0.0', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable( 'customer_entity' ),
                'giftcard_balance',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'Balance',
                    'after' => 'is_active'
                ],
            );
            $installer->getConnection()->addColumn(
                $installer->getTable( 'giftcard_code'),
                'status',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Status',
                    'values' => [
                        0 => 'Enabled',
                        1 => 'Locked',
                        2 => 'Disabled'
                    ]
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable( 'quote' ),
                'giftcard_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'size' => 255,
                    'comment' => 'Gift Card Code',
                    'after' => 'is_active'
                ]
            );
            if (!$installer->tableExists('giftcard_history')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('giftcard_history')
                )
                    ->addColumn(
                        'history_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'nullable' => false,
                            'primary'  => true,
                            'unsigned' => true
                        ],
                        'History ID'
                    )
                    ->addColumn(
                        'giftcard_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'nullable' => false,
                            'unsigned' => true
                        ],
                        'Gift Card ID'
                    )
                    ->addColumn(
                        'customer_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'nullable' => false,
                            'unsigned' => true
                        ],
                        'Customer ID'
                    )
                    ->addColumn(
                        'amount',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '12,4',
                        [],
                        'Amount'
                    )
                    ->addColumn(
                        'action',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'Action'
                    )
                    ->addColumn(
                        'action_time',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false],
                        'Action Time'
                    )
                    ->addForeignKey(
                        $installer->getFkName('giftcard_history', 'history_id', 'giftcard_code', 'giftcard_id'),
                        'giftcard_id',
                        $installer->getTable('giftcard_code'),
                        'giftcard_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $installer->getFkName('giftcard_history', 'history_id', 'customer_entity', 'entity_id'),
                        'customer_id',
                        $installer->getTable('customer_entity'),
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )
                    ->setComment('History Table');
                $installer->getConnection()->createTable($table);
            }
        }


        $installer->endSetup();
    }
}
