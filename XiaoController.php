<?php
/**
 * Created by PhpStorm.
 * User: liyefeng
 * Date: 2019/4/8
 * Time: 9:57 AM
 */

namespace console\controllers;
use GuzzleHttp\Client;
use yii;

class XiaoController extends yii\console\Controller
{
    public function actionIndex()
    {
        $items = [];
        $data = $this->__getUrl("https://www.biqutxt.com/63_63869/");
        file_put_contents('abc.json', json_encode($data));
    }

    public function actionIndex1()
    {
        $items = [];
        for($i=1;$i<22;$i++)
        {
            $data = $this->__getUrl("https://m.biquge5.com/4_4673/all_" . $i . "/");
            $items = array_merge($items, $data);

        }
        file_put_contents('abc.json', json_encode($items));
    }

    public function actionDetail()
    {
        $data = file_get_contents('abc.json');
        $data = json_decode($data, true);
        foreach ($data as $_v)
        {
            $this->__getDetail($_v);
        }
    }

    public function actionDetail1()
    {
        $data = file_get_contents('abc.json');
        $data = json_decode($data, true);
        $i=1;
        foreach ($data as $_v)
        {
            if($i>118)
                $this->__getDetail($_v);
            $i++;
        }
    }

    private function __getUrl($url)
    {
        $data = $this->__curl($url);
        preg_match_all('/<li><a href="([\d]*).html/s', $data, $items);
        return $items[1];
    }

    private function __getDetail($url)
    {
        $data = $this->__curl('https://www.biqutxt.com/63_63869/'.$url.'.html');

        $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');

        preg_match_all('/<h1>(.*?)<\/h1>/s', $data, $title);
        preg_match_all('/<div id="content">(.*?)<\/div>/s', $data, $items);
        $str = $items[1][0];
        $str = str_replace('手机阅读地址：m.biqutxt.com', '', $str);
        $str = str_replace('<br />',"\r\n",$str);
        $str = str_replace('&nbsp;&nbsp;&nbsp;&nbsp;'," ",$str);
        $new_str = "\r\n".$title[1][0]."\r\n".$str;
        file_put_contents('xiaoshuo.txt', $new_str, FILE_APPEND);
        echo $title[1][0]."\r\n";
    }

    private function __getDetail1($url)
    {
        $data = $this->__curl($url);

        $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');

        preg_match_all('/<div class="nr_title" id="nr_title">(.*?)<\/div>/s', $data, $title);
        preg_match_all('/<div id="nr1" style="a{color:blue}">(.*?)<\/div>/s', $data, $items);
        $str = $items[1][0];
        $str = str_replace('<a href="#">本章未完，点击下一页继续阅读</a>', '', $str);
        $str = str_replace('<br />',"\r\n",$str);
        $str = str_replace('&nbsp;&nbsp;&nbsp;&nbsp;'," ",$str);
//echo $title[1][0];die;
        $new_str = $title[1][0]."\r\n".$str;
        file_put_contents('xiaoshuo.txt', $new_str, FILE_APPEND);
        echo $title[1][0]."\r\n";
        preg_match_all('/<a class="pb_next" href="(.*?).html">/s', $data, $next);
        $next = $next[1][0];
        if(strpos($next, '_')){
            $this->__getDetail('https://m.biquge5.com/4_4673/' . $next . '.html');
        }
    }

    private function __curl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl,CURLOPT_USERAGENT,"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36"); //模拟浏览器代理
        //curl_setopt($curl,CURLOPT_HTTPHEADER,["content-type: text/html;charset=gbk"]);
        //curl_setopt($curl, CURLOPT_ACCEPT_ENCODING, "gzip, deflate");
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
}