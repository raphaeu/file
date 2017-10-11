<?php
namespace raphaeu;

class File
{
    private $name = null;
    private $data = null;
    
    function __construct($name=NULL, $data=NULL)
    {
        $this->setName($name);
        if ($data != NULL)
        $this->setData($data);
    }

    
    public function getOnlyName()
    {
        if (strrpos($this->name, '.'))
        {
            return substr($this->name, 0,strrpos($this->name, '.') );
        }else{
            return $this->name;
        }
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getData()
    {
        if (empty($this->data) && $this->exist())
        {
            $this->read();            
        }
        return $this->data;
        
            
    }

    public function setName($name = null)
    {
        if (!empty($name))
        {
            $this->name = $name;
            if ($this->exist())
                $this->read();
        }else{
            $this->name = null;
            $this->data = null;
        }
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function exist() {
        return file_exists($this->name)?true:false;
    }
    
    public function read(){
        if ($this->exist())        
        {
            $data =  file_get_contents($this->name);
            $this->data = $data;
            
        }
        else 
            throw new \ErrorException('Ocorreu um erro ao tentar ler o arquivo: '.$e->getMessage());
               
    }
    public function write(){
        try {
            file_put_contents($this->name, $this->data);
        } catch (\ErrorException $e) {
            throw new \ErrorException('Ocorreu um erro ao tentar gravar o arquivo: '.$e->getMessage());
        }
    }

    
    public function move($location)
    {
        $oldFile = new File($this->getName());
        $this->copy($location);
        $oldFile->delete();
    }   
    
    public function delete() 
    {
        if ($this->exist())
            unlink($this->name);
        $this->name = null;
        $this->data = null;
    }
    
    public function copy($location)
    {
        try {
            if ($this->exist())
            {
                copy($this->name,$location);
                $this->name = $location;
            }else{
                $this->name = $location;
                $this->write();
            }
        } catch (\Exception $e) {
            throw new \ErrorException('Ocorreu ao copiar o arquivo: '.$location);
        }
    }
    
    static function makeMagicNameFile($name)
    {
            // removendo simbolos, acentos etc
            $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýýþÿŔŕ?';
            $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuuyybyRr-';
            $name = strtr($name, utf8_decode($a), $b);
            $name = str_replace(".","-",$name);
            $name = preg_replace( "/[^0-9a-zA-Z\.]+/",'-',$name);
            return utf8_decode(strtolower($name));
    }
    
    static function curl_file_create($filename, $mimetype = '', $postname = '') {
        return "@$filename;filename="
        . ($postname ?: basename($filename))
        . ($mimetype ? ";type=$mimetype" : '');
    }

    static function makeRandomName(){
        return md5(time() .rand());
    }
    
}
