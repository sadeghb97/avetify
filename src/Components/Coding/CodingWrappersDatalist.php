<?php
namespace Avetify\Components\Coding;

use Avetify\Fields\JSDatalist;

class CodingWrappersDatalist extends JSDatalist {
    protected static bool $isPlaced = false;
    public const DATALIST_KEY = "coding_wrappers_datalist";

    public function __construct(){
        $records = self::wrappersList();
        parent::__construct(self::DATALIST_KEY, $records, "slug", "title");
    }

    public static function wrappersList() : array {
        return [
            ["slug" => "plain", "title" => "Plain"],
            ["slug" => "bash", "title" => "Bash"],
            ["slug" => "c", "title" => "C"],
            ["slug" => "cpp", "title" => "CPP"],
            ["slug" => "csharp", "title" => "CSharp"],
            ["slug" => "css", "title" => "CSS"],
            ["slug" => "django", "title" => "Django"],
            ["slug" => "gradle", "title" => "Gradle"],
            ["slug" => "java", "title" => "Java"],
            ["slug" => "javascript", "title" => "JavaScript"],
            ["slug" => "json", "title" => "JSON"],
            ["slug" => "kotlin", "title" => "Kotlin"],
            ["slug" => "php", "title" => "PHP"],
            ["slug" => "php-template", "title" => "PHP-Template"],
            ["slug" => "python", "title" => "Python"],
            ["slug" => "python-repl", "title" => "Python-repl"],
            ["slug" => "scss", "title" => "SCSS"],
            ["slug" => "shell", "title" => "Shell"],
            ["slug" => "sql", "title" => "SQL"],
            ["slug" => "typescript", "title" => "TypeScript"],
            ["slug" => "xml", "title" => "XML"],
            ["slug" => "yaml", "title" => "YAML"],
            ["slug" => "output", "title" => "Output"]
        ];
    }

    public static function placeDatalist() : ?JSDatalist {
        if(!self::$isPlaced) {
            $dl = new CodingWrappersDatalist();
            $dl->place();
            self::$isPlaced = true;
            return $dl;
        }
        return null;
    }
}
