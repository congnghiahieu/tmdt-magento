<?php

namespace UET\PriceRate\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\HTTP\Client\Curl;

class PriceRate extends Template
{
    protected Curl $curl;

    public function __construct(Template\Context $context, Curl $curl)
    {
        parent::__construct($context);
        $this->curl = $curl;
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getPriceRate()
    {
        try {
            $url = 'https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx?b=68';
            $this->curl->get($url);
            $xml_text = $this->curl->getBody();

            $xml = simplexml_load_string($xml_text);
            return $xml;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
