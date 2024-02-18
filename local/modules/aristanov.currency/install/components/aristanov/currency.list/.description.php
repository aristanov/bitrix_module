<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = array(
    "NAME" => GetMessage("CURRENCY_LIST_NAME"),
    "DESCRIPTION" => GetMessage("CURRENCY_LIST_DESCRIPTION"),
    "SORT" => 10,
    "PATH" => array(
        "ID" => "aristanov",
        "CHILD" => array(
            "ID" => "currency",
            "NAME" => GetMessage("CURRENCY_CHILD_NAME"),
            "SORT" => 10,
        )
    ),
);