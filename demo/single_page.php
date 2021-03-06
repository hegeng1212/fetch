<?php
//下面两行使得这个项目被下载下来后本文件能直接运行
$demo_include_path = dirname(__FILE__) . '/../';
set_include_path(get_include_path() . PATH_SEPARATOR . $demo_include_path);

require_once('phpfetcher.php');
class mycrawler extends Phpfetcher_Crawler_Default {
    public function handlePage($page) {

        $params = array(
            'url'     => $page->getUrl(),
            'title'   => '',
            'icon'    => '',
            'img'     => array(),
            'price'   => '',
            'content' => '',
            'good'    => '',
            'num'     => '',
            'follow'  => '',
        );

        $objContent = $page->sel("//div");
        for ($i = 0; $i < count($objContent); ++$i) {
            $objSpan = $objContent[$i]->find("span");
            for ($j = 0; $j < count($objSpan); ++$j) {
                if ($objSpan[$j]->getAttribute('class') == 'cx_price' && $params['price'] == '') {
                    $params['price'] = trim(strip_tags($objSpan[$j]->outertext()));
                }
            }
            $objLi = $objContent[$i]->find("li");
            for ($j = 0; $j < count($objLi); ++$j) {
                if ($objLi[$j]->getAttribute('class') == 'f_1' && $params['good'] == '') {
                    $params['good'] = preg_replace('/[\x80-\xff]/', '', $objLi[$j]->outertext());
                    $params['good'] = trim(strip_tags($params['good']));
                }
                if ($objLi[$j]->getAttribute('class') == 'f_2' && $params['num'] == '') {
                    $params['num'] = preg_replace('/[\x80-\xff]/', '', $objLi[$j]->outertext());
                    $params['num'] = trim(strip_tags($params['num']));
                }
                if ($objLi[$j]->getAttribute('class') == 'f_3' && $params['follow'] == '') {
                    $params['follow'] = preg_replace('/[\x80-\xff]/', '', $objLi[$j]->outertext());
                    $params['follow'] = trim(strip_tags($params['follow']));
                }
            }
            if ($objContent[$i]->getAttribute('class') == 'gy-thumbs') {
                $objImg = $objContent[$i]->find("img");
                for ($j = 0; $j < count($objImg); ++$j) {
                    $params['img'][] = $objImg->getAttribute('src');
                }
            }

            $objImg = $objContent[$i]->find("img");
            for ($j = 0; $j < count($objImg); ++$j) {
                if ($objImg[$j]->getAttribute('class') == 'niu_pic') {
                    $params['icon'] = '牛人专线';
                }
            }
        }
        //打印处当前页面的title
        $res = $page->sel('//h1');
        $params['title'] = trim($res[0]->plaintext);

        var_dump($params);exit;
    }
}

$crawler = new mycrawler();
$arrJobs = array(
    //任务的名字随便起，这里把名字叫qqnews
    //the key is the name of a job, here names it qqnews
    'qqnews' => array( 
        'start_page' => 'http://www.tuniu.com/tours/210204641', //起始网页
        'link_rules' => array(
            /*
             * 所有在这里列出的正则规则，只要能匹配到超链接，那么那条爬虫就会爬到那条超链接
             * Regex rules are listed here, the crawler will follow any hyperlinks once the regex matches
             */
        ),
        //爬虫从开始页面算起，最多爬取的深度，设置为1表示只爬取起始页面
        //Crawler's max following depth, 1 stands for only crawl the start page
        'max_depth' => 1, 
        
    ) ,   
);

//$crawler->setFetchJobs($arrJobs)->run(); //这一行的效果和下面两行的效果一样
$crawler->setFetchJobs($arrJobs);
$crawler->run();
