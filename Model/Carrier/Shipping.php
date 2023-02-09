<?php

namespace HimaMage\SimpleShipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;

class Shipping extends  AbstractCarrier implements CarrierInterface
{

    protected  $_code = 'simpleshipping';
    private ResultFactory $resultFactory;
    private MethodFactory $methodFactory;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        ResultFactory $resultFactory,
        MethodFactory $methodFactory,
        array $data = []
    ) {

        $this->resultFactory = $resultFactory;
        $this->methodFactory = $methodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

    }

    public function collectRates(RateRequest $request)
    {
        if(!$this->getConfigData('active')) {
            return false;
        }

        $result = $this->resultFactory->create();

        $method = $this->methodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethod($this->getConfigData('name'));

        $amount = $this->getShippingPrice();

        $method->setPrice($amount);
        $method->setCost($amount);

        $result->append($method);

        return $result;
    }

    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    public function getShippingPrice()
    {
         $configPrice = $this->getConfigData('price');

         $shippingPrice = $this->getFinalPriceWithHandlingFee($configPrice);

         return $shippingPrice;
    }
}
