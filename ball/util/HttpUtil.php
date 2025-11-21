<?php

namespace ball\util;

use ball\helper\Security;
use Yii;

class HttpUtil
{
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_WWW_URLENCODED = 'www-urlencoded';
    const METHOD_RAW = 'raw';
    const METHOD_FILE = 'file';
    const METHOD_XML = 'xml';

    // ajax return value
    const XHR_SUCCESS = '1';
    const XHR_FAILED = '-1';

    /**
     * @param string $url
     * @param array $data
     * @param string $method
     * @param array $option
     * @return mixed|null
     * @throws \Exception
     */
    public static function curl(string $url, array $data = [], string $method = 'post', array $option = [
        CURLOPT_RETURNTRANSFER => true, // return web page
        CURLOPT_HEADER => false, // don't return headers
        CURLOPT_FOLLOWLOCATION => true, // follow redirects
        CURLOPT_AUTOREFERER => true,
        CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1",
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CONNECTTIMEOUT => 0,
        // timeout 3 seconds
        CURLOPT_TIMEOUT => 30])
    {
        $option[CURLOPT_DNS_USE_GLOBAL_CACHE] = false;
        $option[CURLOPT_DNS_CACHE_TIMEOUT] = 2;

        $method = strtolower($method);
        if ($method == 'post') {
            $option[CURLOPT_POST] = count($data);
            $option[CURLOPT_POSTFIELDS] = http_build_query($data);
        } else if ($method == 'get') {
            if (!empty($data)) {
                $url .= '?' . http_build_query($data);
            }
        } else if ($method == 'www-urlencoded') {
            $option[CURLOPT_HTTPHEADER][] = 'Content-Type: application/x-www-form-urlencoded';
            $option[CURLOPT_POST] = 1;
            $option[CURLOPT_POSTFIELDS] = http_build_query($data);
        } else if ($method == 'raw') {
            $option[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
            $option[CURLOPT_POST] = 1;
            $option[CURLOPT_POSTFIELDS] = json_encode($data);
        } else if ($method == 'file') {
            $option[CURLOPT_POST] = count($data);
            $option[CURLOPT_POSTFIELDS] = $data;
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, $option);
        $httpcode = null;
        $result = null;
        if (!curl_errno($ch)) {
            $result = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
        if ($httpcode == 200) {
            curl_close($ch);
            return $result;
        } else {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("HTTP error, error message: $error, http code: $httpcode, message: $result, method: $method, header: " .
                json_encode($option) . ", data: " . json_encode($data));
        }
    }

    public static function post($field)
    {
        if (isset($_POST[$field])) {
            return $_POST[$field];
        } else {
            return '';
        }
    }

    public static function get($field)
    {
        if (isset($_GET[$field])) {
            return $_GET[$field];
        } else {
            return '';
        }
    }

    /**
     * @return string Client's IP
     */
    public static function ip()
    {
        $isPublicIP = filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        if (!$isPublicIP) {
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { // cloudfront ip format is "211.21.158.139, 54.239.179.97"
                $ip = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
                return $ip[0];
            }
        }
        return $_SERVER["REMOTE_ADDR"];
    }

    public static function isMobile()
    {
        if (preg_match('/(android|bb\d+|meego).+mobile|iPad|oppo|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $_SERVER['HTTP_USER_AGENT'])
            || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($_SERVER['HTTP_USER_AGENT'], 0, 4))
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function port()
    {
        $cfVisitor = json_decode($_SERVER['HTTP_CF_VISITOR']);
        if (isset($cfVisitor->scheme) && $cfVisitor->scheme == 'https') {
            return 443;
        }

        if (!empty($_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO'])) {
            if ($_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO'] == 'https') {
                $port = 443;
            } else {
                $port = 80;
            }
        } else {
            if (!empty($_SERVER["HTTP_X_FORWARDED_PORT"])) {
                $port = $_SERVER['HTTP_X_FORWARDED_PORT'];
            } else {
                $port = $_SERVER['SERVER_PORT'];
            }
        }
        return $port;
    }

    /**
     * @return bool
     */
    public static function isPublicIP()
    {
        return filter_var(self::ip(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) ? true : false;
    }

    public static function deleteAllCookies(string $domain = null)
    {
        // unset cookies
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                if ($domain == null) {
                    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : null;
                }
                setcookie($name, '', time() - 3600, "", $domain);
                setcookie($name, '', time() - 3600, '/', $domain);
            }
        }
    }

    public static function wwwURLEncoded2JSONString(string $result)
    {
        return substr($result, strpos($result, "{"));
    }

    /**
     * @param array $fields
     * @return string
     */
    public static function removeQuery(array $fields): string
    {
        $data = $_GET;

        foreach ($fields as $f) {
            if (isset($data[$f])) {
                unset($data[$f]);
            }
        }
        if (empty($data)) {
            return '';
        }
        return '?' . http_build_query($data);
    }

    /**
     * @param array $queryString
     * @param array $removeData
     * @param array $appendData
     * @return string
     */
    public static function buildQuery(array $queryString, array $removeData = [], array $appendData = []): string
    {
        if (!empty($removeData)) {
            foreach ($removeData as $d) {
                unset($queryString[$d]);
            }
        }

        if (!empty($appendData)) {
            foreach ($appendData as $k => $v) {
                $queryString[$k] = $v;
            }
        }

        if (empty($queryString)) {
            return '';
        }
        return '?' . http_build_query($queryString);
    }

    /**
     * @param string $field
     * @param string $caption
     * @return string
     */
    public static function buildOrderQuery(string $field, string $caption): string
    {
        $order = Yii::$app->request->get("order") == "DESC" ? "ASC" : "DESC";
        $action = Yii::$app->controller->action->id;
        $controller = Yii::$app->controller->id;
        return "<a href=\"/" . $controller . "/" . $action . HttpUtil::buildQuery($_GET, [], ["orderby" => $field, "order" => $order]) . "\">" . $caption . "</a>";
    }

    public static function setCookie(string $key, string $value, int $expire = null, bool $httpOnly = true, string $path = "/", string $domain = null, bool $secure = null)
    {
        if (!isset(Yii::$app->request->cookieValidationKey)) {
            return;
        }
        
        // 自動偵測 HTTPS 環境
        if ($secure === null) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                      || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
                      || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        }
        
        $key = Security::encrypt($key);
        $value = Security::encrypt($value);
        if ($domain == null) {
            $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : null;
        }
        setcookie($key, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    public static function getCookie(string $key, bool $decrypt = true)
    {
        if (!isset(Yii::$app->request->cookieValidationKey)) {
            return false;
        }
        if ($decrypt) {
            $key = Security::encrypt($key);
        }
        if (isset($_COOKIE[$key])) {
            if ($decrypt) {
                return Security::decrypt($_COOKIE[$key]);
            } else {
                return $_COOKIE[$key];
            }
        } else {
            return false;
        }
    }

    public static function deleteCookie(string $key, bool $encrypt = true, string $domain = null)
    {
        if ($encrypt) {
            $key = Security::encrypt($key);
        }

        if (isset($_COOKIE[$key])) {
            unset($_COOKIE[$key]);
            if ($domain == null) {
                $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : null;
            }
            setcookie($key, null, time() - 3600, "", $domain);
            setcookie($key, null, time() - 3600, '/', $domain);
            return true;
        } else {
            return false;
        }
    }
}