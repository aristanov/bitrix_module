<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/aristanov.currency/prolog.php');

use Bitrix\Main\Loader;
use Bitrix\Main\Type;
use Bitrix\Main\Localization\Loc;
use Aristanov\Currency\CurrencyRateTable;

//todo подключение файлов локализации

// Подключаем модуль
if (!Loader::includeModule('aristanov.currency')) {
    throw new Exception('Модуль "aristanov.currency" не найден!');
}

// Запрос на удаление
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST['action'] == 'delete' && check_bitrix_sessid()) {
    
    
    $result = CurrencyRateTable::delete($_REQUEST['id']);
    if (!$result->isSuccess()) {
        // Обработка ошибок удаления
    }
}

// Класс для представления списка
class CCurrencyRatesAdminList extends CAdminList
{
    // Реализация структуры списка и работы с элементами
}

// Создаем объект $adminList
$adminListTableID = 'tbl_currency_rates';
$adminSort = new CAdminSorting($adminListTableID, "date", "desc");
$adminList = new CCurrencyRatesAdminList($adminListTableID, $adminSort);

// Описание заголовков таблицы
$adminList->AddHeaders(array(
    array("id" => "ID", "content" => "ID", "default" => true),
    array("id" => "CODE", "content" => 'Код', "default" => true),
    array("id" => "DATE", "content" => 'Дата', "default" => true),
    array("id" => "COURSE", "content" => 'Курс', "default" => true),
    // Дополнительные поля по необходимости
));

// Загрузка списка валютных курсов
$rsData = CurrencyRateTable::getList(array(
    "select" => array("ID", "CODE", "DATE", "COURSE"),
    "order" => array($by => (strtoupper($order) == "ASC" ? "ASC" : "DESC"))
));

// Метод для подготовки данных для списка
$rsData = new CAdminResult($rsData, $adminListTableID);
$rsData->NavStart();
$adminList->NavText($rsData->GetNavPrint('Курс'));

while ($arRes = $rsData->NavNext(true, "f_")) {
    $row = &$adminList->AddRow($f_ID, $arRes);

    // Установка значений и параметров для строки таблицы
    $row->AddViewField("ID", $f_ID);
    $row->AddInputField("CODE", array("size" => 3));
    $row->AddCalendarField("DATE");
    $row->AddInputField("COURSE", array("size" => 10));

    // Создание действий для каждой строки
    $actions = array();
    $actions[] = array(
        "ICON" => "edit",
        "DEFAULT" => true,
        "TEXT" => 'Редактировать',
        "ACTION" => $adminList->ActionRedirect("currencies_edit.php?ID=" . $f_ID)
    );
    $actions[] = array(
        "ICON" => "delete",
        "TEXT" => 'Удалить',
        "ACTION" => "if(confirm('Удалить?')) " . $adminList->ActionDoGroup($f_ID, "delete")
    );

    $row->AddActions($actions);
}

// Футер таблицы с общим количеством элементов
$adminList->AddFooter(
    array(
        array("title" => 'Всего', "value" => $rsData->SelectedRowsCount()), // Счетчик всех строк
        array("counter" => true, "title" => 'Выбрано', "value" => "0"), // Счетчик выделенных строк
    )
);

// Групповые действия
$adminList->AddGroupActionTable(array(
    "delete" => 'Удалить все',
));

// Вывод
$adminList->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

// Отображение действия - заголовка
$adminList->DisplayList();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
