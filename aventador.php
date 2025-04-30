<?php
require_once "routing/Routing.php";

require_once "interface/Interface.php";
require_once "interface/Placeable.php";
require_once "interface/Styler.php";
require_once "interface/HTMLModifier.php";
require_once "interface/HTMLInterface.php";
require_once "interface/JSInterface.php";

require_once "db/DBConnection.php";
require_once "db/DBFilter.php";
require_once "db/QueryBuilder.php";

require_once"externals/ImageManipulator.php";
require_once"externals/jdf.php";
require_once "externals/gumlet-image/ImageResize.php";
require_once "externals/gumlet-image/ImageResizeException.php";

require_once "models/Filename.php";
require_once "models/DataModel.php";

require_once "files/Filer.php";
require_once "files/ImageUtils.php";

require_once "utils/cli_utils.php";
require_once "utils/echo_utils.php";
require_once "utils/time_utils.php";
require_once "utils/number_utils.php";
require_once "utils/network_utils.php";
require_once "utils/string_utils.php";

require_once "network/URLBuilder.php";
require_once "network/NetworkFetcher.php";
require_once "network/ProxyFetcher.php";
require_once "network/HeadersFetcher.php";
require_once "network/ProxyHeadersFetcher.php";

require_once "entities/BasicEntityProperties.php";
require_once "entities/SBEntityItem.php";
require_once "entities/SetModifier.php";
require_once "entities/BaseSetRenderer.php";
require_once "entities/SetRenderer.php";
require_once "entities/SBSet.php";
require_once "entities/RecordContextMenu.php";
require_once "entities/EntityField.php";
require_once "entities/SBEntity.php";
require_once "entities/EntityUtils.php";
require_once "entities/SortFactor.php";
require_once "entities/FilterFactor.php";
require_once "entities/ValueGetter.php";
require_once "entities/fields/date_fields.php";
require_once "entities/fields/flag_fields.php";
require_once "entities/fields/EntityAvatarField.php";
require_once "entities/fields/EntitySelectField.php";

require_once "fields/JSDataElement.php";
require_once "fields/JSDatalist.php";
require_once "fields/JSDataSet.php";
require_once "fields/JSInputField.php";
require_once "fields/APIMedalField.php";
require_once "fields/JSTextField.php";
require_once "fields/JSACTextField.php";
require_once "fields/JSDynamicSelect.php";
require_once "fields/APISpanField.php";

require_once "components/Submittable.php";
require_once "components/DialogField.php";
require_once "components/JoshButton.php";
require_once "components/NiceDiv.php";
require_once "components/VertDiv.php";
require_once "components/PlaceableTextField.php";
require_once "components/JSField.php";
require_once "components/SpecialField.php";
require_once "components/SpecialTags.php";
require_once "components/WinRateBar.php";
require_once "components/CountrySelector.php";
require_once "components/images/PlaceableImage.php";
require_once "components/images/IconButton.php";
require_once "components/images/CroppableImage.php";
require_once "components/images/CroppingImage.php";
require_once "components/images/ImageCropper.php";
require_once "components/buttons/AbsoluteButton.php";
require_once "components/modifiers/ImageModifiers.php";

require_once "lister/SBListCategory.php";
require_once "lister/SBLister.php";
require_once "lister/DBLister.php";
require_once "lister/RankingList.php";

require_once "table/SBTable.php";
require_once "table/DBTable.php";
require_once "table/JSONTable.php";
require_once "table/fields/SBTableField.php";
require_once "table/fields/SBTableSortField.php";
require_once "table/fields/LinkFields.php";
require_once "table/fields/ImageFields.php";
require_once "table/fields/EditableFields.php";
require_once "table/fields/NumberFields.php";
require_once "table/fields/DateFields.php";
require_once "table/fields/TextFields.php";
require_once "table/fields/FlagFields.php";
require_once "table/fields/SelectField.php";

require_once "crawling/Scrapper.php";
require_once "crawling/RawDocumentLoader.php";

require_once "galrepo/GalleryRepo.php";
require_once "galrepo/ManageGalleryLister.php";

require_once "standings/LeagueStandings.php";
require_once "standings/Competitor.php";

require_once "modules/Flusher.php";
require_once "modules/SecureNetwork.php";
require_once "modules/Printer.php";
require_once "modules/SetPlexer.php";

require_once "forms/FormUtils.php";
require_once "forms/FormButton.php";
require_once "forms/SBForm.php";

require_once "themes/ThemesManager.php";
require_once "themes/green/GreenTheme.php";
require_once "themes/green/GreenTableRenderer.php";
require_once "themes/modern/ModernTheme.php";
require_once "themes/modern/ModernSetRenderer.php";
require_once "themes/modern/ModernGallery.php";
require_once "themes/modern/ModernRatioGallery.php";
require_once "themes/modern/print.php";
require_once "themes/modern/cards.php";
require_once "themes/modern/components.php";

require_once "renderers/PageRenderer.php";
require_once "renderers/AventadorRenderer.php";

require_once "calc/DateStatsCalculator.php";
require_once "calc/IRDateStatsCalculator.php";

require_once "repo/countries/WorldCountries.php";
require_once "repo/countries/World.php";
require_once "repo/countries/CountriesTable.php";

$AVENTADOR_ROOT_PATH = "";
$AVENTADOR_PHYSICAL_ROOT_PATH = "";

define("AVENTADOR_VERSION", "0.11");
define("AVENTADOR_BUILD", 2);

function initAventador($rootPath, $physicalRoot = ""){
    global $AVENTADOR_ROOT_PATH;
    global $AVENTADOR_PHYSICAL_ROOT_PATH;
    $AVENTADOR_ROOT_PATH = $rootPath;
    $AVENTADOR_PHYSICAL_ROOT_PATH = $physicalRoot;
}
