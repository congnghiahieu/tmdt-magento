<?php

namespace UET\News\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\HTTP\Client\Curl;

class News extends Template
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
            $url = 'https://vnexpress.net/rss/kinh-doanh.rss';
            $this->curl->get($url);
            $xml_text = $this->curl->getBody();

            $xml = simplexml_load_string($xml_text);
            for ($i = 0; $i < count($xml->channel->item); $i++) {

                $split = explode("</br>", $xml->channel->item[$i]->description);
                if (count($split) > 1) {
                    $xml->channel->item[$i]->image = $split[0];
                    $xml->channel->item[$i]->realDescription = $split[1];
                }
            }

            return $xml;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
