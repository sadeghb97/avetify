<?php
require_once "routing/Routing.php";
require_once "db/DBConnection.php";

require_once"externals/ImageManipulator.php";
require_once"externals/jdf.php";
require_once "externals/gumlet-image/ImageResize.php";
require_once "externals/gumlet-image/ImageResizeException.php";

require_once "models/Filename.php";

require_once "utils/echo_utils.php";
require_once "utils/time_utils.php";

require_once "network/network_utils.php";
require_once "network/NetworkFetcher.php";
require_once "network/ProxyFetcher.php";
require_once "network/HeadersFetcher.php";
require_once "network/ProxyHeadersFetcher.php";

require_once "entities/FFSet.php";
require_once "entities/EntityField.php";
require_once "entities/SBEntity.php";
require_once "entities/EntityUtils.php";

require_once "components/Placeable.php";
require_once "components/DialogField.php";
require_once "components/Styler.php";
require_once "components/NiceDiv.php";
require_once "components/images/CroppableImage.php";
require_once "components/images/CroppingImage.php";

require_once "lister/SBListCategory.php";
require_once "lister/SBLister.php";
require_once "lister/RankingList.php";

require_once "table/SBTableField.php";
require_once "table/SBTableSortField.php";
require_once "table/SBTable.php";
require_once "table/LinkFields.php";
require_once "table/ImageFields.php";
require_once "table/EditableFields.php";

require_once "crawling/Scrapper.php";
require_once "crawling/RawDocumentLoader.php";

require_once "galrepo/GalleryRepo.php";
require_once "galrepo/ManageGalleryLister.php";

require_once "modules/HTMLInterface.php";
require_once "modules/JSInterface.php";
require_once "modules/Flusher.php";

require_once "forms/FormUtils.php";

require_once "themes/ThemesManager.php";
require_once "themes/classic/ClassicTheme.php";
require_once "themes/green/GreenTheme.php";
require_once "themes/modern/ModernTheme.php";
require_once "themes/modern/print.php";
require_once "themes/modern/cards.php";
require_once "themes/modern/components.php";

$AVENTADOR_ROOT_PATH = "";

function initAventador($rootPath){
    global $AVENTADOR_ROOT_PATH;
    $AVENTADOR_ROOT_PATH = $rootPath;
}
