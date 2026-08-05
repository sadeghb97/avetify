<?php
namespace Avetify\Themes\Green;

use Avetify\AvetifyManager;
use Avetify\Themes\Main\EntitySearcherRenderer;
use Avetify\Themes\Main\AvtTheme;

class GreenEntitySearcherRenderer extends EntitySearcherRenderer {
    public function renderBody(): void {
        $payload = $this->searcher->buildPayload();
        $varName = $payload["payloadVarName"] ?? "__ENTITY_SEARCHER_PAYLOAD__";
        $pageTitle = htmlspecialchars($payload["pageTitle"] ?? "Entities", ENT_QUOTES, "UTF-8");
        $pageSubtitle = htmlspecialchars($payload["pageSubtitle"] ?? "", ENT_QUOTES, "UTF-8");
        $placeholder = htmlspecialchars($payload["searchPlaceholder"] ?? "Start typing...", ENT_QUOTES, "UTF-8");

        echo '<div class="ent-shell">
    <div class="container-fluid">
        <div class="ent-panel">
            <div class="ent-topbar">
                <h1 class="ent-title">' . $pageTitle . '</h1>
                <p class="ent-subtitle">' . $pageSubtitle . '</p>

                <div class="ent-tabs" id="ent_tabs"></div>

                <div class="ent-searchrow">
                    <input id="ent_query" class="ent-input" type="text" autocomplete="off" spellcheck="false"
                           placeholder="' . $placeholder . '" />
                    <div class="ent-stat">
                        <div><span id="ent_stat_entity">—</span></div>
                        <div><span id="ent_stat_count">—</span></div>
                    </div>
                </div>
            </div>
            <div class="ent-body">
                <div id="ent_results" class="ent-results"></div>
                <div id="ent_empty" class="ent-empty" style="display:none"></div>
            </div>
        </div>
    </div>
</div>';

        echo '<script>window.' . $varName . ' = ';
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo ';</script>';

        AvtTheme::importJS(AvetifyManager::assetUrl("components/entity-searcher/entity-searcher.js"));
    }
}
