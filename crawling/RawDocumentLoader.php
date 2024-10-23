<?php

class RawDocumentLoader {
    public static int $EMPTY = 0;
    public static int $VALID = 1;
    public static int $OLD = 2;
    public static int $INVALID = 3;

    public int $status = 0;
    public string $contents = "";
    public bool $freshLoad = false;

    public function __construct(
        public string $remote,
        public string $filename,
        public null | NetworkFetcher $fetcher = null
    ){}

    public function isValid($contents) : bool {
        return true;
    }

    public function isLoaded() : bool {
        return $this->status == self::$VALID;
    }

    public function load(int $dayLimit = 0, bool $localOnly = false) : void {
        if($dayLimit > 0 && file_exists($this->filename)){
            $contentsObjectRaw = file_get_contents($this->filename);
            $contentsObject = json_decode($contentsObjectRaw, true);
            if(isset($contentsObject['time']) && isset($contentsObject['body'])){
                if($this->isValid($contentsObject['body'])){
                    $sep = time() - $contentsObject['time'];
                    $dayLength = 24 * 3600;
                    $sepDays = (int) ($sep / $dayLength);
                    if($dayLimit > $sepDays){
                        $this->status = self::$VALID;
                        $this->contents = $contentsObject['body'];
                        $this->freshLoad = false;
                        return;
                    }
                }
            }
        }

        if(!$localOnly) {
            if ($this->fetcher != null) {
                $rc = $this->fetcher->fetch($this->remote);
            }
            else $rc = curlGetContents($this->remote);

            if ($rc) {
                $this->contents = $rc;
                $this->freshLoad = true;

                if ($this->isValid($rc)) {
                    $this->status = self::$VALID;
                    $this->storeContents();
                }
                else $this->status = self::$INVALID;
            }
        }
    }

    public function storeContents(){
        $contentsObject = [
            "time" => time(),
            "body" => $this->contents
        ];
        $rawContentsObject = json_encode($contentsObject);
        file_put_contents($this->filename, $rawContentsObject);
    }
}
