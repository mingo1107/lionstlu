<?php

namespace frontend\widget;


use ball\helper\Pagination;
use yii\base\Widget;

class Paging extends Widget
{
    const PAGE_SIZE = 14;
    const MOBILE_PAGE_SIZE = 10;
    const PAGE_AMOUNT = 10;
    // page query string
    const QSTART = 'start';
    const QSIZE = 'size';
    public $size;
    public $start;
    public $count;
    /**
     * @var Pagination
     */
    private $page;
    private $url;
    private $pageurl;
    // Google style pagination
    private $linkStart;
    private $linkLimit;

    public function init()
    {

    }

    public function run()
    {
        $this->page = new Pagination($this->count);
        if (isset($this->size)) {
            $this->page->applyLimit($this->start, $this->size);
        } else {
            $this->page->applyLimit($this->start, self::PAGE_SIZE);
        }
        $this->setUrl();
        $this->setPageUrl();
        $this->setLinks();
        return $this->render('paging', ['paging' => $this]);
    }

    private function setUrl()
    {
        $this->url = $_SERVER['QUERY_STRING'];
        if (isset($_GET[self::QSTART])) {
            $this->url = str_ireplace('&' . self::QSTART . '=' . $_GET[self::QSTART], '', $this->url);
            $this->url = str_ireplace(self::QSTART . '=' . $_GET[self::QSTART], '', $this->url);
        }
        if (isset($_GET[self::QSIZE])) {
            $this->url = str_ireplace('&' . self::QSIZE . '=' . $_GET[self::QSIZE], '', $this->url);
            $this->url = str_ireplace(self::QSIZE . '=' . $_GET[self::QSIZE], '', $this->url);
        }
        if (strpos($this->url, '&') === 0) {
            $this->url = substr($this->url, 1);
        }
        $this->url = '?' . $this->url;
    }

    private function setPageUrl()
    {
        if ($this->url == '?') {
            $this->pageurl = self::QSTART;
        } else {
            $this->pageurl = '&' . self::QSTART;
        }
    }

    private function setLinks()
    {
        // 計算起始頁
        $this->linkLimit = $this->page->getCurrentPage() + (intval(self::PAGE_AMOUNT - 1) / 2) + 1;
        if ($this->page->getCurrentPage() - intval(self::PAGE_AMOUNT / 2) < 1) {
            $this->linkStart = 1;
        } else {
            $this->linkStart = $this->page->getCurrentPage() - intval(self::PAGE_AMOUNT / 2);
        }
    }

    public function getPageUrl()
    {
        return $this->pageurl;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getLinkStart()
    {
        return $this->linkStart;
    }

    public function getLinkLimit()
    {
        return $this->linkLimit;
    }

    public function getPage()
    {
        return $this->page;
    }

    public static function parseStart($start)
    {
        $start = intval($start);
        if ($start > 0) {
            return $start;
        } else {
            return 0;
        }
    }

    public static function parseSize($size)
    {
        $size = intval($size);
        if ($size > 0) {
            return $size;
        } else {
            return self::PAGE_SIZE;
        }
    }
}