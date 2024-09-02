<?php
namespace Privatcoollib;

use GuzzleHttp\Client;

class ExchangedAmount
{
    private $from;
    private $to;
    private $amount;
    private $client;

    public function __construct( $from,  $to,  $amount)
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
        $this->client = new Client([
            'base_uri' => 'https://www.cbr.ru',
            'verify' => false
        ]);
    }

    public function toDecimal()
    {
        $rates = $this->getRates();
        $rate = $rates[$this->from][$this->to]['buy'];

        return $this->amount * $rate;
    }

    private function getRates()
    {
        $response = $this->client->request('GET', '/scripts/XML_daily.asp');

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Error fetching exchange rates');
        }

        $xml = simplexml_load_string($response->getBody()->getContents());

        $rates = [];

        foreach ($xml->Valute as $valute) {
            $charCode = (string) $valute->CharCode;
            $rate = (float) str_replace(',', '.', $valute->Value);
            $nominal = (int) $valute->Nominal;

            if ($charCode === 'USD' || $charCode === 'EUR' || $charCode === 'RUB') {
                $rates[$charCode]['UAH'] = [
                    'buy' => $rate / $nominal
                ];
            }
        }

        return $rates;
    }
}
