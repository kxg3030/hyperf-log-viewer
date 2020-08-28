<?php

namespace Hyperf\LogViewer\Concrete;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Collection;

class Paginate
{
    public $size = 2;

    private $show = 5;


    public function render(Collection $logCollection, string $link, int $currentPage = 1) {
        $logRoute   = ApplicationContext::getContainer()->get(RequestInterface::class)->getUri()->getPath() . $link;
        $pagination = "";
        if ($logCollection->isNotEmpty()) {
            $totalPage = $this->totalPage($logCollection->count());
            list($beginPage, $endPage) = $this->beginAndEndPage($currentPage, $totalPage);
            $prev         = $currentPage - 1 < 1 ? 1 : $currentPage - 1;
            $prevDisabled = $currentPage <= 1 ? "disabled" : "";
            $pagination   .= sprintf($this->template(), $prev, $prevDisabled, sprintf($logRoute, $prev), "Prev");
            for ($i = $beginPage; $i <= $endPage; $i++) {
                if ($currentPage == $i) {
                    $pagination .= sprintf($this->template(), $i, "active", sprintf($logRoute, $i), $i);
                } else {
                    $pagination .= sprintf($this->template(), $i, "", sprintf($logRoute, $i), $i);
                }
            }
            $next         = $currentPage + 1 >= $totalPage ? $totalPage : $currentPage + 1;
            $nextDisabled = $currentPage >= $totalPage ? "disabled" : "";
            $pagination   .= sprintf($this->template(), $next, $nextDisabled, sprintf($logRoute, $next), "Next");
        }
        return $pagination;
    }

    private function template() {
        return '<li data-id="%s" class="page-item %s file-page-link"><a class="page-link" href="%s">%s</a></li>';
    }

    private function beginAndEndPage($currentPage, $totalPage) {
        $beginPage       = $currentPage;
        $offset          = floor($this->show / 2);
        $beginLeftOffset = $beginPage - $offset;
        $beginPage       = $beginLeftOffset < 1 ? 1 : $beginLeftOffset;
        $endPageOffset   = $beginPage + $this->show - 1;
        $endPage         = $endPageOffset > $totalPage ? $totalPage : $endPageOffset;
        $beginPage       = $endPage - $this->show + 1;
        $beginPage       = $beginPage < 1 ? 1 : $beginPage;
        return [$beginPage, $endPage];
    }

    private function totalPage(int $count) {
        return ceil($count / $this->size);
    }
}