<?php
namespace Aristanov\Currency;

use Bitrix\Main\Entity;
use Bitrix\Main\Type;


class CurrencyRateTable extends Entity\DataManager {
    public static function getTableName() {
        return 'aristanov_currency_rates';
    }

    public static function getMap() {
        return [
            new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ]),

            new Entity\StringField('CODE', [
                'required' => true,
                'validation' => function() {
                    return [
                        new Entity\Validator\Length(null, 3),
                    ];
                }
            ]),

            new Entity\DatetimeField('DATE', [
                'required' => true,
                'default_value' => function() {
                    return new Type\DateTime();
                }
            ]),

            new Entity\FloatField('COURSE', [
                'required' => true
            ])
        ];
    }
}