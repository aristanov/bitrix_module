<?php

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


class aristanov_currency extends CModule
{
    var $MODULE_ID = 'aristanov.currency';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME = 'Модуль валютных курсов';
    var $MODULE_DESCRIPTION = 'Модуль для работы с валютными курсами';
    var $PARTNER_NAME = 'Аристанов';
    var $PARTNER_URI = 'http://aristanov.dev/';

    public function __construct()
    {
        include(__DIR__ . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = $arModuleVersion['MODULE_NAME'];
        $this->MODULE_DESCRIPTION = $arModuleVersion['MODULE_DESCRIPTION'];
    }

    function DoInstall()
    {
        global $APPLICATION;

        ModuleManager::registerModule($this->MODULE_ID);
        $this->createTables();
        $this->createAgent();

          // Регистрация административной страницы
          CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'].'/local/modules/aristanov.currency/admin',
            $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin',
            true, true
        );
    }

    function DoUninstall()
    {
        global $APPLICATION;

        $this->dropTables();
        $this->deleteAgent();
        ModuleManager::unRegisterModule($this->MODULE_ID);

        DeleteDirFiles(
            $_SERVER['DOCUMENT_ROOT'].'/local/modules/aristanov.currency/admin',
            $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin'
        );

    }

    function createTables()
    {
        $connection = Application::getConnection();
        try {
            $connection->queryExecute("
                CREATE TABLE IF NOT EXISTS `aristanov_currency_rates` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `code` varchar(3) NOT NULL,
                    `date` datetime NOT NULL,
                    `course` float NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `code_date` (`code`, `date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
        } catch (SqlQueryException $e) {
            throw new \Bitrix\Main\DB\SqlQueryException(
                'Ошибка при создании таблицы: ' . $e->getMessage()
            );
        }
    }

    function dropTables()
    {
        $connection = Application::getConnection();
        try {
            $connection->queryExecute("DROP TABLE IF EXISTS `aristanov_currency_rates`;");
        } catch (SqlQueryException $e) {
            throw new \Bitrix\Main\DB\SqlQueryException(
                'Ошибка при удалении таблицы: ' . $e->getMessage()
            );
        }
    }


    function createAgent() {
        $agentFunction = 'Aristanov\\Currency\\CurrencyAgent::updateRates();';
        CAgent::AddAgent(
            $agentFunction, // имя функции
            $this->MODULE_ID, // идентификатор модуля
            "N", // агент не критичен к кол-ву запусков
            86400, // интервал запуска агента — 24 часа (86400 секунд)
            "", // дата первой проверки на запуск
            "Y", // агент активен
            "", // дата первого запуска
            30 // приоритет
        );
    }
    
    function deleteAgent() {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
    }
}
