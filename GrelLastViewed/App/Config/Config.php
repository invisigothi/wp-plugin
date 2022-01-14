<?
namespace App\Config;

class Config
{
    const GREL_WIDGET_NAME = 'GREL LAST VIEWED';
    const GREL_WIDGET_DESCRIPTION = 'Виджет для вывода недавно просмотренных страниц и записей';
    const GREL_COOKIE_PREFIX = 'grel_';
    const DEFAULT_COOKIE_LIVE = 3600;
    const DEFAULT_MAX_PAGE = 15;

    function CheckLang()
    {
        if ($_GET['lang'] === 'RU' || !isset($_GET['lang'])){
                $currentLang = 'RU';
        }else{
                $currentLang = 'EN';
        }
        return $currentLang;
    }
}