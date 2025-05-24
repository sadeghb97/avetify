<?php
namespace Avetify\Modules;

class SetPlexer {
    public function __construct(public string $separator, public bool $trimmed = false){}

    public function plex($rawString){
        if(!$rawString) return [];
        $out = explode($this->separator, $rawString);
        if(!$this->trimmed) return $out;

        for ($i=0; count($out) > $i; $i++) $out[$i] = trim($out[$i]);
        return $out;
    }

    public function deplex($set){
        $str = "";
        foreach ($set as $item){
            if($str) $str .= ($this->trimmed ? trim($this->separator) : $this->separator);
            $str .= $item;
        }
        return $str;
    }
}
