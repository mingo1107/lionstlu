<?php

namespace ball\helper;


class Pagination
{
    // default page size
    const PAGE_SIZE = 14;
    /**
     * @var int
     */
    private $count;
    /**
     * @var int
     */
    private $size;
    /**
     * @var int
     */
    private $start;
    /**
     * @var int
     */
    private $currentPage;

    public function __construct($count)
    {
        $count = intval($count);
        if ($count < 0) {
            $this->count = 0;
        } else {
            $this->count = $count;
        }
    }

    public function applyLimit($start = 0, $limit = self::PAGE_SIZE)
    {
        $start = intval($start);
        $limit = intval($limit);
        if ($start < 0) {
            $this->start = 0;
        } else {
            $this->start = $start;
        }

        if ($limit <= 0) {
            $this->size = self::PAGE_SIZE;
        } else {
            $this->size = $limit;
        }

        $this->currentPage = intval($this->start / $this->size) + 1;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getCount()
    {
        return $this->count;
    }

    public static function getOffset()
    {
        if (!isset($_GET['start'])) {
            return 0;
        }
        $size = intval($_GET['start']);
        return $size > 0 ? $size : 0;
    }
}

