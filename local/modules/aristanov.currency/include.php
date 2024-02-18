<?php

Bitrix\Main\Loader::registerAutoLoadClasses(
    'aristanov.currency', 
    array(
        'Aristanov\\Currency\\CurrencyRateTable' => 'lib/currencyratetable.php',
        'Aristanov\\Currency\\CurrencyAgent' => 'lib/currencyagent.php', 
    )
);