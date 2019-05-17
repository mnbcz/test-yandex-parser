<?php
/**
 * Created by PhpStorm.
 * User: mnbcz
 * Date: 16.05.2019
 * Time: 11:30
 */

// Для получения текста страницы через curl
require_once 'common/Curl.php';
use common\Curl;

// Запросы к DOM через jQuery синтаксис
require_once 'libs/phpQuery/phpQuery/phpQuery.php';

// Класс который содержит имена полей со страницы телефонов
require_once 'PhoneKeys.php';


class PhoneParser
{

    /**
     * Возвращает массив объектов PhoneKeys.
     * @param $url
     * @return array
     * @throws Exception - Если не удалось получить html.
     */
    public function getItems($url){

        // Получаем текст страницы
        $html = Curl::getContent($url, $info);
        if($html == false || $info['http_code'] != 200){
            throw new Exception("Html не получен. Код ответа = {$info['http_code']}.");
        }

        // Добавляем разметку к phpQuery
        phpQuery::newDocumentHTML($html);

        // Список элементов
        $itemsDom = pq('.n-snippet-list .n-snippet-cell2');

        // Массив данных, который возвращается
        $items = array();

        foreach ($itemsDom as $itemDom){
            $PhoneKeys = new PhoneKeys();
            $PhoneKeys->title = pq($itemDom)->find(".n-snippet-cell2__title")->eq(0)->text();
            $PhoneKeys->price = pq($itemDom)->find(".n-snippet-cell2__main-price-wrapper .price")->eq(0)->text();
            $PhoneKeys->price = preg_replace("#[^0-9]#isu", "", $PhoneKeys->price);
            $items[] = $PhoneKeys;
        }

        return $items;

    }


    /**
     * Возвращает html разметку для элементов items.
     * @param $items - Массив объектов PhoneKeys.
     * @return string
     */
    public function getHtmlForItems($items){

        ob_start();
        foreach ($items as $item){ ?>
             <strong>Заголовок: <?php echo $item->title ?></strong>
             <br>
             <strong>Цена: <?php echo $item->price ?></strong>
             <br><br>
        <?php }
        return ob_get_clean();

    }


}


// https://market.yandex.ru/catalog/54726/list?clid=817&onstock=1&local-offers-first=1

$obj = new PhoneParser();

if(empty($_REQUEST['url'])){
    echo "Укажите url к странице";
    die();
}

try {
    $items = $obj->getItems($_REQUEST['url']);
} catch (Exception $e){
    echo $e->getMessage();
    die();
}
echo $obj->getHtmlForItems($items);