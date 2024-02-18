<?php
namespace Aristanov\Currency;

use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Loader;
use Aristanov\Currency\CurrencyRateTable;

class CurrencyAgent
{
    public static function updateRates()
    {
        Loader::includeModule('aristanov.currency');

        // URL Центрального банка РФ для получения курсов валют в XML
        $url = 'https://www.cbr.ru/scripts/XML_daily.asp';

        $httpClient = new HttpClient();
        $response = $httpClient->get($url);

        if ($response === false) {
            return "Aristanov\\Currency\\CurrencyAgent::updateRates();";
        }

        try {
            $xml = new \SimpleXMLElement($response);
            $date = (string)$xml['Date'];
            
            foreach ($xml->Valute as $valute) {
                $code = (string)$valute->CharCode;
                $value = (string)$valute->Value;
                $course = str_replace(',', '.', $value);

                if (in_array($code, ['USD', 'EUR'])) {
                    self::saveRateToDB($code, $date, $course);
                }
            }
            
        } catch (\Exception $e) {
            //todo Обработка исключения, возможно логирование
        }
        
        return "Aristanov\\Currency\\CurrencyAgent::updateRates();";
    }

    private static function saveRateToDB($code, $date, $course)
    {
        $dateObject = new Date($date, 'd.m.Y');
        
        $existingRate = CurrencyRateTable::getList([
            'filter' => [
                'CODE' => $code,
                'DATE' => $dateObject
            ],
            'limit' => 1
        ])->fetch();

        if (!$existingRate) {
            CurrencyRateTable::add([
                'CODE' => $code,
                'DATE' => $dateObject,
                'COURSE' => (float)$course
            ]);
        } else {
            CurrencyRateTable::update($existingRate['ID'], [
                'COURSE' => (float)$course
            ]);
        }
    }
}