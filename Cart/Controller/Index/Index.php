<?php 
namespace Czar\Cart\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;

class Index extends Action
{


protected $formKey;   
protected $cart;
protected $product;
protected $_pageFactory;
protected $_session;
protected $serializer;
protected $ProductRepository;

public function __construct(
\Magento\Framework\App\Action\Context $context,
\Magento\Framework\Data\Form\FormKey $formKey,
\Magento\Checkout\Model\Cart $cart,
\Magento\Catalog\Model\Product $product,
 \Magento\Catalog\Model\ProductRepository $ProductRepository,
\Magento\Customer\Model\Session $session,
\Magento\Framework\Serialize\SerializerInterface $serializer,
\Magento\Framework\View\Result\PageFactory $PageFactory,
array $data = []) {
    $this->formKey = $formKey;
    $this->_pageFactory = $PageFactory;
    $this->cart = $cart;
    $this->serializer = $serializer;
    $this->product = $product; 
    $this->ProductRepository = $ProductRepository;
    $this->_session = $session;    
    parent::__construct($context);
}

public function execute()
 {

  
  $page = $this->_pageFactory->create();
  $productId =10;
  $id = $this->_session->getId();
  $additionalOptions['additional_options'] = [
            'label' => 'Print Style',
            'value' => 'Test',
        ];
  $additionalOptions['test'] = [
            'label' => 'Print Stylessss',
            'value' => 'value',
        ];
  $additionalOptions['name'] = [
            'label' => 'Name',
            'value' => 'ttttttesting',
        ];
  if(!$this->_session->isLoggedIn()){
  $params = array(
                'form_key' => $this->formKey->getFormKey(),
                'product' => $productId, //product Id
                'qty'   =>1, //quantity of product
                'price' => 1               
            );   
   }else{
   		$params = array(
                'form_key' => $this->formKey->getFormKey(),
                'product' => $productId, //product Id
                'qty'   =>1, //quantity of product
                'customer_id'   =>$id,
                'price' => 1                
            );
   }           
    //Load the product based on productID   
    $_product = $this->ProductRepository->getById($productId);
    
    $_product->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions)); 
    // $_product->addCustomOption('test', $this->serializer->serialize($additionalOptions));
    // $_product->addCustomOption('name', $this->serializer->serialize($additionalOptions));           
               
    $this->cart->addProduct($_product, $params);
    $this->cart->save();
    return $page;
 
}
}