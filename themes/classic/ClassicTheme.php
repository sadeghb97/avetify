<?php

class ClassicTheme extends ThemesManager {
    public function headerTags(){
        parent::headerTags();
        self::importMainStyles($this->ROOT_PATH);
        self::importStyle($this->ROOT_PATH . "themes/classic/style.css");
        self::importJS($this->ROOT_PATH . "themes/classic/jslib.js");
    }

    public static function importMainStyles(string $arp){
        self::importStyle($arp . "themes/classic/style.css");
    }
}

class CropperClassicTheme extends ClassicTheme {
    public function headerTags(){
        parent::headerTags();
        self::importCdnJS("https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js",
            "sha512-6lplKUSl86rUVprDIjiW8DuOniNX8UDoRATqZSds/7t6zCQZfaCe3e5zcGaQwxa8Kpn5RTM9Fvl3X2lLV4grPQ==");
        self::importCdnStyle("https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css",
            "sha512-cyzxRvewl+FOKTtpBzYjW6x6IAYUCZy3sGP40hn+DQkqeluGRCax7qztK2ImL64SA+C7kVWdLI6wvdlStawhyw==");
    }
}

class LadiesClassicTheme extends ClassicTheme {
    public function headerTags(){
        parent::headerTags();
        self::importJS("https://unpkg.com/vue-multiselect@2.1.0");
        self::importStyle("https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css");
    }
}

class ExternalCropperClassicTheme extends ClassicTheme {
    public function headerTags(){
        parent::headerTags();
        $this->importCropperCSS();
        $this->importCropperJS();
        $this->importBootstrap();
    }
}
