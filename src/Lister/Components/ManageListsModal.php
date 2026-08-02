<?php
namespace Avetify\Lister\Components;

use Avetify\AvetifyManager;
use Avetify\Components\Modals\AvtModal;

class ManageListsModal extends AvtModal {
    public function placeTemplateBody(): void {
        require AvetifyManager::assetPath("components/lister/templates/arrange.html");
    }

    public function setupJs(): void {
        echo 'setupManageModal(this);';
    }
}