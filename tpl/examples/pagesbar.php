<h1 class="mt-4 mb-4">Постраничный бар</h1>
<div class="row">
    <div class="col">
        [code=Php]
        IncludeCom(
            "_dev/paginator",
            [
                "css" => "justify-content-center",
                "pageUrl" => GetCurUrl("page=" . M_PAGINATOR_PAGE),
                "firstPageUrl" => GetCurUrl("page=" . M_DELETE_PARAM),
                "total" => 25,
                "perPage" => 5,
                "curPage" => Get("page", 1)
            ]
        );
        [/code]
    </div>
    <div class="col">
        <?php
        IncludeCom(
            "_dev/paginator",
            [
                "css"          => "justify-content-center",
                "pageUrl"      => GetCurUrl("page=" . M_PAGINATOR_PAGE),
                "firstPageUrl" => GetCurUrl("page=" . M_DELETE_PARAM),
                "total"        => 25,
                "perPage"      => 5,
                "curPage"      => Get("page", 1)
            ]
        );
        ?>
    </div>
</div>