<?php
namespace Czar\Cart\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Unserialize\Unserialize;
use \Magento\Framework\Serialize\Serializer\Json;


 
class OrderItemAdditionalOptions implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    protected $Unserialize;

    protected $Serializer;

    public function __construct(
        Unserialize $Unserialize,
        Json $Json
    ){
        $this->Unserialize = $Unserialize;
        $this->Serializer = $Json;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $quote = $observer->getQuote();
            $order = $observer->getOrder();
            $quoteItems = [];

            // Map Quote Item with Quote Item Id
            foreach ($quote->getAllVisibleItems() as $quoteItem) {
                $quoteItems[$quoteItem->getId()] = $quoteItem; //130
            }

            foreach ($order->getAllVisibleItems() as $orderItem) {
                $quoteItemId = $orderItem->getQuoteItemId();
                $quoteItem = $quoteItems[$quoteItemId];
                $additionalOptions = $quoteItem->getOptionByCode('additional_options');
     // Get Order Item's other options
                    $options = $orderItem->getProductOptions();
                    // Set additional options to Order Item
                   if($this->isSerialized($additionalOptions->getValue())){
                        $options['options'] = $this->Unserialize->unserialize($additionalOptions->getValue());
                   }else{
                    $options['options'] = $this->Serializer->unserialize($additionalOptions->getValue());
                   }
                    

                    $orderItem->setProductOptions($options);
            }
        }

        catch (\Exception $e) {
            // catch error if any
            $e->getMessage();
        }
    }
    private function isSerialized($value)
    {
        return (boolean) preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }
}