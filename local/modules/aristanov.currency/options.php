<?php
$module_id = "aristanov.currency"; 

// Подключаем модуль
if (!CModule::IncludeModule($module_id)) {
    return;
}

// todo Загрузка списка валют из ЦБ или вашего источника
$arCurrencies = array(
    "USD" => "Доллар США",
    "EUR" => "Евро",
    // Все доступные валюты
);

// Проверка отправки формы
if ($_SERVER["REQUEST_METHOD"] == "POST" && check_bitrix_sessid()) {
    foreach ($arCurrencies as $currencyCode => $currencyName) {
        $optionName = "load_currency_" . $currencyCode;
        $optionValue = isset($_POST[$optionName]) ? 'Y' : 'N';
        COption::SetOptionString($module_id, $optionName, $optionValue);
    }
}

// Загрузка текущих настроек
$arSettings = array();
foreach ($arCurrencies as $currencyCode => $currencyName) {
    $optionName = "load_currency_" . $currencyCode;
    $arSettings[$optionName] = COption::GetOptionString($module_id, $optionName);
}
?>

<form method="post" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($module_id) ?>&amp;lang=<?= LANGUAGE_ID ?>">
    <?= bitrix_sessid_post(); ?>

    <table class="adm-detail-content-table edit-table">
        <?php foreach ($arCurrencies as $currencyCode => $currencyName): ?>
            <tr>
                <td width="50%" class="adm-detail-content-cell-l">
                    <?= htmlspecialcharsbx($currencyName) ?>:
                </td>
                <td width="50%" class="adm-detail-content-cell-r">
                    <?php $optionName = "load_currency_" . $currencyCode; ?>
                    <input type="checkbox" name="<?= htmlspecialcharsbx($optionName) ?>" value="Y"
                           <?php if ($arSettings[$optionName] == "Y") echo "checked"; ?> />
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <input type="submit" name="save" value="<?= GetMessage("MAIN_SAVE") ?>"
           title="<?= GetMessage("MAIN_OPT_SAVE_TITLE") ?>" class="adm-btn-save" />
</form>