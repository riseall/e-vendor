<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupplierItemService
{
    protected $url;
    protected $namespace;

    public function __construct()
    {
        $this->url = env('QAD_WSA_URL', 'http://192.168.122.63:24087/wsa/wsatest');
        $this->namespace = 'http://ws.imi.co.id/wsatest';
    }

    public function searchItems($keyword, $page = 1, $limit = 20)
    {
        // Susun XML Body secara dinamis
        $soapBody = <<<XML
        <Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
            <Body>
                <getSuplierItem01 xmlns="{$this->namespace}">
                    <parDBLogical>qaddb</parDBLogical>
                    <p_search>{$keyword}</p_search>
                    <p_page>{$page}</p_page>
                    <p_limit>{$limit}</p_limit>
                </getSuplierItem01>
            </Body>
        </Envelope>
        XML;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction'   => 'urn:iris:wsa-phapros:getSuplierItem01',
            ])
                ->withBody($soapBody, 'text/xml')
                ->post($this->url);

            if ($response->failed()) {
                throw new \Exception("QAD Service Error: " . $response->status());
            }

            return $this->parseResponse($response->body());
        } catch (\Exception $e) {
            Log::error("QAD_CONNECTION_FAILED: " . $e->getMessage());
            return ['items' => [], 'hasMore' => false];
        }
    }

    protected function parseResponse($xmlString)
    {
        $cleanXml = str_ireplace(['SOAP-ENV:', 'SOAP:', 'ns0:'], '', $xmlString);
        $xml = simplexml_load_string($cleanXml);

        $results = [];
        $hasMore = false;

        if (isset($xml->Body->getSuplierItem01Response->tt_vp_item->tt_vp_itemRow)) {
            foreach ($xml->Body->getSuplierItem01Response->tt_vp_item->tt_vp_itemRow as $row) {
                $results[] = [
                    'id'   => (string) $row->id,
                    'desc' => (string) $row->v_desc,
                ];
            }
        }

        // Ambil output parameter p_hasMore
        if (isset($xml->Body->getSuplierItem01Response->p_hasMore)) {
            $hasMore = filter_var((string)$xml->Body->getSuplierItem01Response->p_hasMore, FILTER_VALIDATE_BOOLEAN);
        }

        return [
            'items'   => $results,
            'hasMore' => $hasMore
        ];
    }
}
