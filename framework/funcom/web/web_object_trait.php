<?php
namespace FunCom\Web;

/**
 * Description of TWebObject
 *
 * @author david
 */

use FunCom\Element;
use FunCom\Template\ETemplateType;
use FunCom\Template\Template;
use FunCom\Cache\Cache;

trait WebObjectTrait
{
    protected $viewFileName = '';
    protected $cacheFileName = null;
    protected $viewName = '';
    protected $className = '';
    protected $dirName = '';
    protected $code = '';
    protected $path = '';
    protected $parentView = null;
    protected $parentType = null;
    protected $fatherTemplate = null;
    protected $fatherUID = '';
    protected $templatePath = '';

    public function __construct(WebObjectInterface $parent)
    {
        parent::__construct($parent);

        $this->fatherTemplate = $parent->getFatherTemplate();
        $this->fatherUID = $parent->getFatherUID();
    }
    
    public function getCacheFileName(?string $viewName = null): string
    {
        if ($this->cacheFileName === null) {
            if ($viewName === null) {
                $viewName = $this->viewName;
            }
            $this->cacheFileName = SRC_ROOT . Cache::cacheFilenameFromView($this->viewName);
        }
        return $this->cacheFileName;
    }

    public function getFatherTemplate(): ?Template
    {
        return $this->fatherTemplate;
    }

    public function getFatherUID(): string
    {
        return $this->fatherUID;
    }

    public function getParentType()
    {
        return $this->parentType;
    }

    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    public function getTemplateType()
    {
        return $this->templateType;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getDirName(): string
    {
        return $this->dirName;
    }

    public function getViewFileName(): string
    {
        return $this->viewFileName;
    }

    public function getViewName(): string
    {
        return $this->viewName;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}
