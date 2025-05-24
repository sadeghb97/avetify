<?php
namespace Avetify\Models;

class Filename {
    public string $dir = "";
    public string $pureName = "";
    public string $pureFilename = "";
    public string $extension = "";

    public function __construct(public string $filename){
        if(str_contains($this->filename, "/")){
            $pos = strrpos($this->filename, "/");
            $this->dir = substr($this->filename, 0, $pos);
            $this->pureFilename = substr($this->filename, $pos + 1);
        }
        else $this->pureFilename = $this->filename;

        if(str_contains($this->pureFilename, ".")){
            $pos = strrpos($this->pureFilename, ".");
            $this->pureName = substr($this->pureFilename, 0, $pos);
            $this->extension = substr($this->pureFilename, $pos + 1);
        }
        else $this->pureName = $this->pureFilename;
    }
}
