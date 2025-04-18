<?php

class JSInputField {
    public static function initJs(){
        $theme = new ThemesManager();
        $theme->importAvnJSFields();
    }
}