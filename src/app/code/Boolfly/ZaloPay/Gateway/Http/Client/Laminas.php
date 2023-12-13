<?php

/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   ZaloPay
 */

namespace Boolfly\ZaloPay\Gateway\Http\Client;

use Magento\Framework\HTTP\LaminasClientFactory;
use Magento\Framework\HTTP\LaminasClient;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;

/**
 * Class Laminas
 *
 * @package Boolfly\ZaloPay\Gateway\Http\Client
 */
class Laminas implements ClientInterface
{
    /**
     * @var LaminasClientFactory
     */
    private $clientFactory;

    /**
     * @var ConverterInterface | null
     */
    private $converter;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param LaminasClientFactory         $clientFactory
     * @param Logger                    $logger
     * @param ConverterInterface | null $converter
     */
    public function __construct(
        LaminasClientFactory $clientFactory,
        Logger $logger,
        ConverterInterface $converter = null
    ) {
        $this->clientFactory = $clientFactory;
        $this->converter     = $converter;
        $this->logger        = $logger;
    }

    /**
     * @param TransferInterface $transferObject
     * @return array
     * @throws ClientException
     * @throws ConverterException
     * @throws \Laminas_Http_Client_Exception
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $log    = [
            'request' => $transferObject->getBody(),
            'request_uri' => $transferObject->getUri()
        ];
        $result = [];
        /** @var LaminasClient $client */
        $client = $this->clientFactory->create();
        //// $client->setConfig($transferObject->getClientConfig());
        $client->setOptions($transferObject->getClientConfig());
        $client->setMethod($transferObject->getMethod());
        $client->setParameterPost($transferObject->getBody());
        $client->setHeaders($transferObject->getHeaders());
        $client->setUrlEncodeBody($transferObject->shouldEncode());
        $client->setUri($transferObject->getUri());

        try {
            //// $response        = $client->request();
            $response        = $client->send();
            $result          = $this->converter ? $this->converter->convert($response->getBody()) : $response->getBody();
            $log['response'] = $result;
            // } catch (\Laminas_Http_Client_Exception $e) {
            //     throw new ClientException(
            //         __($e->getMessage())
            //     );
        } catch (ConverterException $e) {
            throw $e;
        } finally {
            $this->logger->debug($log);
        }

        return $result;
    }
}
