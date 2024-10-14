<?php
class Scrapper {
    public $contents;
    public $cursor = 0;
    public $found = false;

    public $altCursor = 0;
    public $altFound = false;

    public $message = "";
    public $scrapped;

    public function __construct($contents) {
        $this->contents = $contents;
        $this->cursor = 0;
    }

    public function setContents($contents) {
        $this->contents = $contents;
        $this->cursor = 0;
        $this->found = false;
        $this->message = "";
        $this->scrapped = "";
    }

    public function prune($s) {
        $this->contents = str_replace($s, "", $this->contents);
    }

    public function pushClone() {
        return new Scrapper($this->scrapped);
    }

    public function push() {
        $this->setContents($this->scrapped);
    }

    public function pushAfter() {
        if(strlen($this->contents) > ($this->cursor + 1)) {
            $this->setContents(substr($this->contents, $this->cursor + 1));
        }
        else $this->setContents("");
    }

    public function seek($str) {
        $pos = strpos($this->contents, $str, $this->cursor);
        if ($pos !== false) {
            $this->found = true;
            $pos += strlen($str) - 1;
            if (strlen($this->contents) - 1 > $pos) $pos++;
            $this->cursor = $pos;
        } else {
            $this->found = false;
        }
    }

    //safe
    public function remains() : string {
        if(strlen($this->contents) > $this->cursor) {
            return substr($this->contents, $this->cursor);
        }
        return "";
    }

    public function find($startStr, $endStr, $fixedCursor = false) {
        $scr = $this->safeFind($startStr, $endStr, $this->cursor);
        if ($this->altFound) {
            $this->found = true;
            if (!$fixedCursor) $this->cursor = $this->altCursor;
            $this->scrapped = $scr;
        } else {
            $this->found = false;
            $this->message = $scr;
        }
    }

    public function cfind($startStr, $endStr, $callback) {
        $this->find($startStr, $endStr);
        if($this->found){
            $callback($this->pushClone());
        }
    }

    public function after($startStr = null, $fixedCursor = false) {
        $pos = strpos($this->contents, $startStr, $this->cursor);
        if ($pos !== false) {
            $pos += strlen($startStr);
            $this->found = true;
            if (!$fixedCursor) $this->cursor = $pos;
            $this->scrapped = substr($this->contents, $pos);
        } else {
            $this->found = false;
        }
    }

    public function before($startStr, $fixedCursor = false) {
        $pos = ($this->contents !== null) ? strpos($this->contents, $startStr, $this->cursor) : false;
        if ($pos !== false) {
            $this->found = true;
            if (!$fixedCursor) $this->cursor = $pos;
            $this->scrapped = substr($this->contents, 0, $pos);
        } else {
            $this->found = false;
        }
    }

    public function safeFind($startStr, $endStr, $startIndex) {
        $startPos = ($this->contents !== null) ? strpos($this->contents, $startStr, $startIndex) : false;
        if ($startPos === false) {
            $this->altFound = false;
            return "StartNotFound";
        }
        $endPos = strpos($this->contents, $endStr, $startPos + strlen($startStr));
        if ($endPos === false) {
            $this->altFound = false;
            return "EndNotFound";
        }

        $sLength = strlen($startStr);
        try {
            $scr = substr($this->contents, $startPos + $sLength, $endPos - $startPos - $sLength);
            $this->altFound = true;
            $this->altCursor = $endPos + strlen($endStr);
            return $scr;
        } catch (Exception $ex) {
            $this->altFound = false;
            return "LengthError";
        }
    }

    public function contains($needle){
        return strpos($this->contents, $needle) !== FALSE;
    }
}



