<?php
namespace UET\Weather\Block;

use Magento\Framework\View\Element\Template;

class Weather extends Template {
    protected $appid="439d4b804bc8187953eb36d2a8c26a02";
    
    public function _prepareLayout() 
    {
        return parent::_prepareLayout();
    }


    public function getAppId() {
        return $this->appid;
        exit;
    }

    public function getWeather()
    {
        try {
            $url = "https://openweathermap.org/data/2.5/weather?id=1581130&appid=439d4b804bc8187953eb36d2a8c26a02";
            $this->curl->get($url);
            $res = $this->curl->getBody();

            $xml = simplexml_load_string($xml_text);
            return $xml;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
