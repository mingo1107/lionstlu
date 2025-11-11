<?php

use backend\widget\Paging;

/* @var $paging Paging */

if ($paging->getPage()->getCount() > $paging->getPage()->getSize()) { ?>
    <div id="pagination-row" class="text-center">
        <nav>
            <ul class="pagination">
                <?php if ($paging->getPage()->getCurrentPage() > 1) {
                    echo '<li aria-label="Previous"><a class="page-link" title="上一頁" href="' . $paging->getUrl() . $paging->getPageUrl() . "=" .
                        ($paging->getPage()->getStart() - $paging->getPage()->getSize()) . '"><i class="fa fa-chevron-left" aria-hidden="true"></i></a></li>';
                }
                for ($i = $paging->getLinkStart(); $i <= $paging->getLinkLimit(); ++$i) {
                    if ($i > 0) {
                        if (($i - 1) * $paging->getPage()->getSize() >= $paging->getPage()->getCount()) break;

                        if ($i == $paging->getPage()->getCurrentPage()) {
                            echo '<li class="active"><a class="js-void" href="#">' . $i . '</a></li>';
                        } else {
                            echo '<li><a href="' . $paging->getUrl() . $paging->getPageUrl() . "=" . (($i - 1) * $paging->getPage()->getSize()) . '" title="第' . $i . '頁">' . $i . '</a></li>';
                        }
                    }
                }
                if ($paging->getPage()->getStart() < $paging->getPage()->getCount() - $paging->getPage()->getSize()) {
                    echo '<li aria-label="Next"><a class="page-link" title="下一頁" href="' . $paging->getUrl() . $paging->getPageUrl() . "=" .
                        ($paging->getPage()->getStart() + $paging->getPage()->getSize()) . '"><i class="fa fa-chevron-right" aria-hidden="true"></i></a></li>';
                } ?>
            </ul>
        </nav>
    </div>
    <!--分頁_結束-->
<?php } ?>

