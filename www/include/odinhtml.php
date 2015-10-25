<?php

class OdinHTML extends DOMDocument
{
    public function createDiv($id,$name,$class,$content) {
        $newNode = $this->createOdinElement('div',$id,$name,$class,$content);
        return($newNode);
    }

    public function createSpan($id,$name,$class,$content) {
        $newNode = $this->createOdinElement('span',$id,$name,$class,$content);
        return($newNode);
    }

    public function createTable($id,$name,$class) {
        $newNode = $this->createOdinElement('table',$id,$name,$class,'');
        return($newNode);
    }

    public function createForm($id,$name,$class, $method, $action ) {
        $newNode = $this->createOdinElement('form',$id,$name,$class,'');
        if ($method) {$newNode->setAttribute('method',$method);}
        if ($action) {$newNode->setAttribute('action',$action);}
        return($newNode);
    }

    private function createOdinElement($tag,$id,$name,$class,$content) {
        $newNode = $this->createElement($tag, $content);
        if ($id) {$newNode->setAttribute('id',$id);}
        if ($name) {$newNode->setAttribute('name',$name);}
        if ($class) {$newNode->setAttribute('class',$class);}
        return($newNode);
    }
}

?>
