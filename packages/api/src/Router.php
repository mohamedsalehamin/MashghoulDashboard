<?php

namespace Tasawk\Api;


class Router {
    public $methd;
    public $uri;
    public $callback;


    public function __construct() {
        die(__CLASS__);
        if ($base) {
            $this->setBase('items');
        }

        throw_if(is_null($this->getBaseNamespace()), 'API base doesn\'t configured');
        $baseUri ='Api' . '/' . $this->getBaseNamespace() . '/';
        $fullUri = $baseUri . $uri;
        return Router::middleware(['api'])
            ->$method($fullUri, $this->getFilteredBase() . $classMethod);

    }

    /**
     * @param mixed $methd
     * @return Router
     */
    public function setMethd($methd) {
        $this->methd = $methd;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMethd() {
        return $this->methd;
    }

    /**
     * @param mixed $uri
     * @return Router
     */
    public function setUri($uri) {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * @param mixed $callback
     * @return Router
     */
    public function setCallback($callback) {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallback() {
        return $this->callback;
    }

}