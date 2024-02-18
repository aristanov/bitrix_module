<?php
if(!empty($arResult['CURRENCIES'])) {
    foreach($arResult['CURRENCIES'] as $currency) {
        echo "ID: {$currency['ID']}, Code: {$currency['CODE']}, Rate: {$currency['RATE']}";
        // И другая информация о курсе валюты
    }
    // Постраничная навигация
} else {
    echo GetMessage("CURRENCY_NO_DATA");
}