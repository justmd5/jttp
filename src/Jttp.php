<?php
/**
 * Created for jttp.
 * File: Http.php
 * User: 丁海军
 * Date: 18/07/09
 * Time: 下午4:54.
 */

namespace Justmd5\Jttp;

use Exception;

/**
 * Class Http.
 */
class Jttp
{
    /**
     * user agent.
     *
     * @var string
     */
    protected static $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36';
    /**
     * @var
     */
    protected static $headers;

    /**
     * 发起一个HTTP/HTTPS的请求
     *
     * @param string       $method  请求类型    GET | POST...
     * @param string       $url     接口的URL
     * @param array|string $params  接口参数   ['content'=>'test', 'format'=>'json']或者json字符串;
     * @param array        $headers 扩展的包头信息
     * @param array        $files   图片信息
     *
     * @return string
     */
    public static function request($method, $url, $params = [], array $headers = [], $files = [])
    {
        if (!function_exists('curl_init')) {
            exit('Need to open the curl extension');
        }
        $method = strtoupper($method);
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_USERAGENT, static::$userAgent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 6);
        curl_setopt($ci, CURLOPT_TIMEOUT, $files ? 30 : 3);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ci, CURLOPT_HEADER, false);
        //合并本身的数据
        is_array($headers) || $headers = [];
        $headers = is_array(static::$headers) ? array_merge(static::$headers, $headers) : $headers;
        if (!function_exists('curl_file_create')) {
            function curl_file_create($filename, $mime_type = '', $post_name = '')
            {
                return "@$filename;filename=".($post_name ?: basename($filename)).($mime_type ? ";type=$mime_type" : '');
            }
        }
        switch ($method) {
            case 'PUT':
            case 'POST':
            case 'PATCH':
                $method == 'POST' || curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($files)) {
                    foreach ($files as $index => $file) {
                        $params[$index] = curl_file_create($file);
                    }
                    phpversion() > '5.5' and curl_setopt($ci, CURLOPT_SAFE_UPLOAD, false);
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                    $headers[] = 'Expect: ';
                    $headers[] = 'Content-Type: multipart/form-data';
                } else {
                    //judge  array
                    if (is_string($params)) {
                        json_decode($params);
                        //如果是json字符串加header头信息
                        if ((json_last_error() == JSON_ERROR_NONE)) {
                            $headers[] = 'Expect: ';
                            $headers[] = 'Content-Type: application/json';
                        }
                    }
                    curl_setopt($ci, CURLOPT_POSTFIELDS, is_array($params) ? http_build_query($params) : $params);
                }
                break;
            case 'GET':
            case 'HEAD':
            case 'DELETE':
            case 'OPTIONS':
                $method == 'GET' || curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method);
                empty($params) or $url .= (strpos($url, '?') ? '&' : '?').(is_array($params) ? http_build_query($params) : $params);
                break;
        }
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);
        curl_setopt($ci, CURLOPT_URL, $url);
        $headers and curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ci);
        //发生错误
        if (curl_errno($ci) > 0) {
            error_log('curl错误：'.curl_errno($ci).' : '.curl_error($ci).'入参:'.json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));
        }
        curl_close($ci);

        return $response;
    }

    /**
     * set user agent.
     *
     * @param string $userAgent
     */
    public static function setUserAgent($userAgent)
    {
        empty($userAgent) or static::$userAgent = $userAgent;
    }

    /**
     * @return string
     */
    public static function getUserAgent()
    {
        return self::$userAgent;
    }

    /**
     * set headers ['X-Served-By:something','X-Served-By'=>'otherSomething'].
     *
     * @param array $headers
     *
     * @return mixed
     */
    public static function setHeaders($headers)
    {
        if (!is_array($headers) || count($headers) <= 0) {
            return false;
        }
        $useHeaders = [];
        array_walk($headers, function ($v, $k) use (&$useHeaders) {
            $useHeaders[] = strpos($v, ':') !== false ? $v : $k.':'.$v;
        });
        if (is_array(static::$headers) && count(static::$headers) > 0) {
            static::$headers = array_merge(static::$headers, $useHeaders);
        } else {
            static::$headers = $useHeaders;
        }
    }

    /**
     * @return mixed
     */
    public static function getHeaders()
    {
        return self::$headers;
    }

    /**
     * static call.
     *
     * @param string $method request method.
     * @param array  $args   request params.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $method = strtoupper($method);
        if (!in_array($method, [
            'GET',
            'POST',
            'DELETE',
            'PUT',
            'PATCH',
            'HEAD',
            'OPTIONS',
        ])) {
            throw new Exception("method $method not support", 400);
        }
        array_unshift($args, $method);

        return call_user_func_array([
            __CLASS__,
            'request',
        ], $args);
    }
}
