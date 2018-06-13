<?php

class Pagination
{

    private $max = 10;

    private $index = 'page';

    private $current_page;

    private $total;

    private $limit;

    public function __construct($total, $currentPage, $limit, $index)
    {

        $this->total = $total;

        $this->limit = $limit;

        $this->index = $index;

        $this->amount = $this->amount();

        $this->setCurrentPage($currentPage);
    }

    public function get()
    {

        $links = null;

        $limits = $this->limits();

        $html = '<ul class="pagination">';

        for ($page = $limits[0]; $page <= $limits[1]; $page++) {
            if ($page == $this->current_page) {
                $links .= '<li class="active"><a href="#">' . $page . '</a></li>';
            } else {
                $links .= $this->generateHtml($page);
            }
        }

        if (!is_null($links)) {

            if ($this->current_page > 1)

                $links = $this->generateHtml(1, '&lt;') . $links;
            if ($this->current_page < $this->amount)
                $links .= $this->generateHtml($this->amount, '&gt;');
        }
        $html .= $links . '</ul>';
        return $html;
    }

    private function generateHtml($page, $text = null)
    {

        if (!$text)

            $text = $page;
        $currentURI = rtrim($_SERVER['REQUEST_URI'], '/') . '/';
        $currentURI = preg_replace('~/page-[0-9]+~', '', $currentURI);

        return
            '<li><a href="' . $currentURI . $this->index . $page . '">' . $text . '</a></li>';
    }

    private function limits()
    {

        $left = $this->current_page - round($this->max / 2);

        $start = $left > 0 ? $left : 1;
        if ($start + $this->max <= $this->amount) {
            $end = $start > 1 ? $start + $this->max : $this->max;
        } else {
            $end = $this->amount;
            $start = $this->amount - $this->max > 0 ? $this->amount - $this->max : 1;
        }
        return
            array($start, $end);
    }

    private function setCurrentPage($currentPage)
    {

        $this->current_page = $currentPage;

        if ($this->current_page > 0) {

            if ($this->current_page > $this->amount)
                $this->current_page = $this->amount;
        } else
            $this->current_page = 1;
    }

    private function amount()
    {
        return ceil($this->total / $this->limit);
    }
}