<?php

namespace Mageplaza\GiftCard\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class AddGiftcard extends Command
{

    const QTY = 'qty';
    protected $giftcardFactory;
    protected $helperData;

    public function __construct(
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory,
        \Mageplaza\GiftCard\Helper\Data $helperData
    )
    {
        parent::__construct();
        $this->giftcardFactory = $giftcardFactory;
        $this->helperData = $helperData;
    }

    protected function configure()
    {

        $options = [
            new InputOption(
                self::QTY,
                null,
                InputOption::VALUE_REQUIRED,
                'qty'
            )
        ];

        $this->setName('giftcard:add')
            ->setDescription('Demo command line')
            ->setDefinition($options);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $enableGiftcard = $this->helperData->getEnable();
        if($enableGiftcard == 1){
            $length = $this->helperData->getCodeLength();
            $qtyDefault = $this->helperData->getNumOfCode();
            if ($qty = $input->getOption(self::QTY)) {
                $qtyDefault = $qty;
            }
            $this->addGiftCard($qtyDefault,$length);
            $output->writeln("added " . $qtyDefault . " gift card success");
        }else{
            $output->writeln("module is off");
        }
        return $this;
    }

    protected function addGiftCard($qty,$length){
        for ($i = 0; $i < $qty; $i++){
            $giftcard = $this->giftcardFactory->create();
            $code = $this->helperData->randomCode($length);
            $data = [
                'code' => $code,
                'balance' => 10,
                'amount_used' => 0,
                'created_from' => 'AdminCLI'
            ];
            $giftcard->setData($data)->save();
        }
    }
}
