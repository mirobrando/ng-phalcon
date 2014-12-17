<?php

namespace mirolabs\phalcon\Framework\View;

use Phalcon\Mvc\View as PhalconView;

class View extends PhalconView
{

    protected $moduleName;

    /**
     * @param mixed $moduleName
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }

    /**
     * @return ManagementPath
     */
    private function getManagementPath()
    {
        return $this->getDI()->get('managementPath');
    }

    protected function _engineRender($engines , $viewPath , $silence, $mustClean, $cache)
    {
        $notExists = true;
        $viewsDir = $this->_viewsDir;
        $basePath = $this->_basePath;
        $viewsDirPath = $basePath . $viewsDir . $viewPath;


        if (is_object($cache)) {
            $renderLevel = $this->_renderLevel;
            $cacheLevel = $this->cacheLevel;

            if ($renderLevel > $cacheLevel) {
                if (!$cache->isstarted()) {
                    $key = null;
                    $lifetime = null;
                    $viewOptions = $this->_options;
                    if (is_array($viewOptions) && array_key_exists('cache', $viewOptions)) {
                        $cacheOptions = $viewOptions['cache'];
                        if (is_array($cacheOptions) && array_key_exists('key', $cacheOptions)) {
                            $key = $cacheOptions['key'];
                        }
                        if (is_array($cacheOptions) && array_key_exists('lifetime', $cacheOptions)) {
                            $lifetime = $cacheOptions['lifetime'];
                        }
                    }
                }
                if (is_null($key)) {
                    $key = md5($viewPath);
                }

                $cachedView = $cache->start($key, $lifetime);
                if (!is_null($cachedView)) {
                    $this->_content = $cachedView;
                    return;
                }
            }

            if (!$cache->isfresh()) {
                return;
            }
        }


        $viewParams = $this->_viewParams;
        $eventManager = $this->_eventManager;

        foreach ($engines as $extension => $engine) {

            $viewEnginePath = $this->getManagementPath()->getTemplatePath(
                $this->moduleName,
                $viewsDir,
                $viewPath,
                $extension);

            if (file_exists($viewEnginePath)) {
                if (is_object($eventManager)) {
                    $this->_activeRenderPath = $viewEnginePath;
                    $status = $eventManager->fire("view:beforeRenderView", $this, $viewEnginePath);
                    if (!$status) {
                        continue;
                    }
                }

                $engine->render($viewEnginePath, $viewParams, $mustClean);
                $notExists = false;

                if (is_object($eventManager)) {
                    $eventManager->fire("view:afterRenderView", $this);
                }
            }
        }


        if ($notExists) {
            if (is_object($eventManager)) {
                $this->_activeRenderPath = $viewEnginePath;
                $eventManager->fire("view:notFoundView", $this);
            }

            if (!$silence) {
                throw new PhalconView\Exception("View '" . $viewsDirPath . "' was not found in the views directory");
            }
        }
    }

}