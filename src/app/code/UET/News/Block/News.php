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

    public function getNews()
    {
        $now = strtotime("now");
        try {
            $url = 'https://vnexpress.net/rss/kinh-doanh.rss';
            $this->curl->get($url);
            $xml_text = $this->curl->getBody();

            $xml = simplexml_load_string($xml_text);
            for ($i = 0; $i < count($xml->channel->item); $i++) {

                $split = explode("</br>", $xml->channel->item[$i]->description);
                if (count($split) > 1) {
                    $xml->channel->item[$i]->image = $split[0];
                    $img_count = preg_match('/src="([\w\d\s\:\/\/\.\?\&\=-]*)"/', $xml->channel->item[$i]->image, $img_tag_match);
                    if ($img_count > 0) {
                        $xml->channel->item[$i]->image = $img_tag_match[1];
                    }
                    $pubDate = strtotime($xml->channel->item[$i]->pubDate);
                    $timeDiff = $now - $pubDate;
                    if ($timeDiff <= 59) {
                        $xml->channel->item[$i]->pubDate = "".$timeDiff."s trước";
                    } elseif ($timeDiff < 3600) {
                        $xml->channel->item[$i]->pubDate = "".floor($timeDiff / 60)."' trước";
                    }
                    elseif ($timeDiff < 86400) {
                        $xml->channel->item[$i]->pubDate = "".floor($timeDiff / 3600)."h trước";
                    } elseif ($timeDiff < 86400 * 15) {
                        $xml->channel->item[$i]->pubDate = "".floor($timeDiff / 86400)." ngày trước";
                    } else {
                        $xml->channel->item[$i]->pubDate = date("dd/MM/yyyy", $pubDate);
                    }
                    $xml->channel->item[$i]->realDescription = $split[1];
                }
            }

            return $xml;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
