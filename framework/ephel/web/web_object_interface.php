<?php
 
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Ephel\Web;

use FunCom\ElementInterface;
use Ephel\Template\TemplateInterface;

/**
 * Description of TObject
 *
 * @author david
 */
 

 interface WebObjectInterface extends HttpTransportInterface, TemplateInterface, ElementInterface {
 
    public function getCacheFileName();
    public function getJsCacheFileName();
    public function getCssCacheFileName();
    public function getClassName();
    public function getActionName();
    public function getViewFileName();
    public function getControllerFileName();
    public function getJsControllerFileName();
    public function getCssFileName();
    public function getViewName();
    public function getParameters();
    
    
}