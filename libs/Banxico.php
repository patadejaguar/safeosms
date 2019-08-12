<?php

class Banxico
{
    const banxicourl = 'http://www.banxico.org.mx:80/DgieWSWeb/DgieWS?WSDL';
    private $_client;
    private $_debug = false;

    public function getExRate()
    {
        $client = $this->_getClient();
        try
        {
            $result = $client->tiposDeCambioBanxico();
        }
        catch (SoapFault $e)
        {
            return $e->getMessage();
        }
        if(!empty($result))
        {
            $dom = new DOMDocument();
            $dom->loadXML($result);
            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('bm', "http://ws.dgie.banxico.org.mx");
            $val = $xpath->evaluate("//*[@IDSERIE='SF60653']/*/@OBS_VALUE");
            return ($val->item(0)->value);
        }

    }

    /**
     * @return SoapClient
     */

    private function _getClient()
    {
        if(empty($this->_client)) {
            $this->_client = $this->_setClient();
        }
        return $this->_client;
    }

    /**
     * @return SoapClient
     */
    private function _setClient()
    {
        return new SoapClient(null,
            array('location' => self::banxicourl,
            'uri'      => 'http://DgieWSWeb/DgieWS?WSDL',
            'encoding' => 'ISO-8859-1',
            'trace'    => $this->_getDebug()
            ));
    }

    private function _getDebug()
    {
        return $this->_debug;
    }
}

?>