<?php
namespace Avetify\Entities\Models;

class PaginationConfigs {
    public int $recordsCount = 0;
    public bool $paginationOnBottom = false;

    public function __construct(public string $namespace, public int $pageSize) {}

    public function getCurrentPage() : int {
        $receivedPage = intval($_GET[$this->getPageKey()] ?? 1) ?? 1;
        if($receivedPage < 1) $receivedPage = 1;
        $lastPage = $this->getLatestPage();
        return min($receivedPage, $lastPage);
    }

    public function getLatestPage() : int {
        if($this->pageSize == 0) return 1;
        return ceil($this->recordsCount / $this->pageSize);
    }

    public function getPageKey() : string {
        return $this->namespace . "_page";
    }
}
