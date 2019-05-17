<?php
/**
 * Created by PhpStorm.
 * User: mnbcz
 * Date: 16.05.2019
 * Time: 11:37
 */

namespace common;

class Curl
{


    /**
     * Curl опции по умолчанию.
     * @var array
     */
    protected static $optionsDefault = array(

        // Документация curl
        // https://curl.haxx.se/libcurl/c/CURLOPT_COOKIEFILE.html

        // CURLOPT_AUTOREFERER - TRUE для автоматической установки поля Referer: в запросах,
        // перенаправленных заголовком Location:.
        CURLOPT_AUTOREFERER => TRUE,

        // CURLOPT_SSL_VERIFYPEER  - FALSE для остановки cURL от проверки сертификата узла сети.
        // Альтернативные сверяемые сертификаты могут быть указаны с помощью параметра CURLOPT_CAINFO
        // или директории с сертификатами, указываемой параметром CURLOPT_CAPATH.
        // По умолчанию равно TRUE начиная с версии cURL 7.10.
        // Дистрибутив по умолчанию устанавливается начиная с версии cURL 7.10.
        // CURLOPT_SSL_VERIFYHOST - Когда значение проверки равно 0,
        // соединение успешно, независимо от имен в сертификате.
        // Используйте эту способность с осторожностью!
        // Значение по умолчанию для этой опции - 2.
        // Чтобы скачивать страницы https нужно установить в 0 (но передавать пароли при этом не нужно)
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,

        // Возвращать результат, а не выводить в браузер
        CURLOPT_RETURNTRANSFER => true,

        // CURLOPT_FOLLOWLOCATION  - TRUE для следования любому заголовку "Location: ",
        // отправленному сервером в ответе (это происходит рекурсивно,
        // PHP будет следовать за всеми посылаемыми заголовками "Location: ", за исключением случая,
        // когда установлена константа CURLOPT_MAXREDIRS).
        CURLOPT_FOLLOWLOCATION => true,
        // CURLOPT_MAXREDIRS   -  Максимальное количество принимаемых редиректов.
        // Используйте этот параметр вместе с параметром CURLOPT_FOLLOWLOCATION.
        CURLOPT_MAXREDIRS => 6,

        // CURLOPT_HEADER  - TRUE для включения заголовков в вывод.
        CURLOPT_HEADER => 0,

        // CURLOPT_CONNECTTIMEOUT  - Количество секунд ожидания при попытке соединения.
        // Используйте 0 для бесконечного ожидания.
        CURLOPT_CONNECTTIMEOUT => 60,
        // CURLOPT_TIMEOUT - Максимально количество секунд для выполнения cURL-функций.
        CURLOPT_TIMEOUT => 60,

        // CURLOPT_ENCODING    - Содержимое заголовка "Accept-Encoding: ".
        // Это позволяет декодировать запрос.
        // Поддерживаемыми кодировками являются "identity", "deflate" и "gzip".
        // Если передана пустая строка, "", посылается заголовок, содержащий все поддерживаемые типы кодировок.
        CURLOPT_ENCODING => 'gzip,deflate,br',

        // CURLOPT_USERAGENT   - Содержимое заголовка "User-Agent: ", посылаемого в HTTP-запросе.
        // Opera (OPR/56.0.3051.116)
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36 OPR/56.0.3051.116',
        // CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:63.0) Gecko/20100101 Firefox/63.0',

        // CURLOPT_REFERER - Содержимое заголовка "Referer: ", который будет использован в HTTP-запросе.
        // CURLOPT_URL - Загружаемый URL. Данный параметр может быть также установлен при инициализации сеанса с помощью curl_init().

        // CURLOPT_HTTPHEADER  - Массив HTTP-заголовков которые нужно установить,
        // в формате array('Content-type: text/plain', 'Content-length: 100')
        CURLOPT_HTTPHEADER => array(

            // Accept - Какие mime типы поддерживаются для ответа
            // */* - Любые MIME типы.
            // q=0.9 - приоритет предыдущего значения до 1
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',

            // 'Accept-Encoding' => 'gzip, deflate, br',

            // 'Accept-Language':
            // 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3' - Firefox
            // 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7' - Opera
            'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',

            // Если отправленное значение является keep-alive, то соединение является постоянным и не закрывается,
            // что позволяет выполнять последующие запросы к тому же серверу.
            'Connection' => 'keep-alive',

            // Cache-Control
            // Определяет кэширование.
            // 'Cache-Control' => 'max-age=0' - Определяет максимальное время, при котором
            // ресурс будет считаться действующим.
            // В отличие от Expires, эта директива относится ко времени запроса.
            // Чтобы отключить кэширование:
            // Cache-Control: no-cache, no-store, must-revalidate
            'Cache-Control' => 'max-age=0'

        ),



    );


    /**
     * Возвращает текст из $url.
     * Для проверки кода ответа, нужно использовать параметр $info, который передается по ссылке, и содержит
     * результат вызова функции $info = curl_getinfo($ch);
     * Можно передать опции curl (или заменить опции по-умолчанию) в параметре $userOptions.
     * @param $url - урл к странице.
     * @param array $info - резултат выполнения функции curl_getinfo($ch);
     * @param array $userOptions - curl опции.
     * @return mixed - текст или false.
     */
    public static function getContent($url, &$info = array(), $userOptions = array()){

        // Переопределяем опции по-умолчанию
        $options = array(
            // Урл с которого загрузить контент
            CURLOPT_URL => $url,
        );

        // Переопределяем опции, последние опции заменяют предыдущие
        $options = array_replace_recursive(self::$optionsDefault, $options, $userOptions);

        // Создание ресурса cURL
        $ch = curl_init();
        // Установка опций запроса
        curl_setopt_array($ch, $options);

        // Если установлена опция CURLOPT_RETURNTRANSFER, при успешном завершении будет возвращен результат,
        // а при неудаче - FALSE.
        $result = curl_exec($ch);

        // Код ответа:
        // $info = curl_getinfo($ch);
        /**
        array(21) {
        ["url"] => string(106) "https://www.avito.ru/api/8/items/1583272073?key=af0deccbgcgidddjgnvljitntccdduijhdinfgjgfjir&includeRefs=1"
        ["content_type"] => string(31) "application/json; charset=utf-8"
        ["http_code"] => int(403)
        ["header_size"] => int(370)
        ["request_size"] => int(314)
        ["filetime"] => int(-1)
        ["ssl_verify_result"] => int(0)
        ["redirect_count"] => int(0)
        ["total_time"] => float(0,143915) // Общее время выполнения транзакции в секундах последней передачи
        ["namelookup_time"] => float(0,002954)
        ["connect_time"] => float(0,004455)
        ["pretransfer_time"] => float(0,103752)
        ["size_upload"] => float(0)
        ["size_download"] => float(164)
        ["speed_download"] => float(1139)
        ["speed_upload"] => float(0)
        ["download_content_length"] => float(-1)
        ["upload_content_length"] => float(0)
        ["starttransfer_time"] => float(0,143784) // Время в секундах до передачи первого байта данных
        ["redirect_time"] => float(0)
        ["certinfo"] => array(0) {
        }
        }
         */
        $info = curl_getinfo($ch);

        // Закрываем ресурс curl
        curl_close($ch);

        return $result;

    }

}