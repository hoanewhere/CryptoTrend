<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsListController extends Controller
{
    const SEARCH_KEYWORD = '仮想通貨';
    const MAX_SHOWED_NEWS = 20;

    public function index() {

        // test
        // $news_list = $this->reloadNews();
        // Log::debug('Newsデータ:' . print_r($news_list, true));

        return view('crypto.newsList');
    }


    public function reloadNews() {
        // キーワード検索したいときのベースURL 
        $api_base_url_front = "https://news.google.com/news/rss/search/section/q/";
        $api_base_url_back = "?hl=ja&gl=JP&ned=jp";

        //　キーワードの文字コード変更
        $query = urlencode(mb_convert_encoding("仮想通貨", "UTF-8", "auto"));
        Log::debug('エンコード結果'.print_r($query, true));

        // APIへのリクエストURL生成
        $api_url = $api_base_url_front . $query . $api_base_url_back;

        // APIにアクセス、結果をsimplexmlに格納
        $contents = file_get_contents($api_url);
        $xml = simplexml_load_string($contents);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        Log::debug('XML形式：'.print_r($xml, true));

        //記事エントリを取り出す
        $data = $array['channel']['item'];
        Log::debug('XML->item：'.print_r($data, true));

        //記事のタイトルとURLを取り出して配列に格納
        $list = array();
        for ($i = 0; $i < count($data); $i++) {
            if($i >= self::MAX_SHOWED_NEWS) {
                break;
            }
            $list[$i]['title'] = mb_convert_encoding($data[$i]['title'] ,"UTF-8", "auto");
            $list[$i]['url'] = mb_convert_encoding($data[$i]['link'] ,"UTF-8", "auto");
            $date = mb_convert_encoding($data[$i]['pubDate'] ,"UTF-8", "auto");
            $list[$i]['date'] = date('Y-m-d H:i:s', strtotime($date));
            $list[$i]['source'] = mb_convert_encoding($data[$i]['source'] ,"UTF-8", "auto");
        }
        return $list;
    }
}
