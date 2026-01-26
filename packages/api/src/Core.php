<?php

namespace Tasawk\Api;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use TorMorten\Eventy\Facades\Eventy;

class Core implements Responsable {

    private $message;
    private $data;
    private $errors;
    private $status;
    private $formatErrors = false;
    /**
     * @var bool
     */
    private bool $returnAsObject = false;

    private $attributes;


    function setStatusErrorConditional() {
        if (!is_array($this->errors)) {
            throw new \Exception("Errors array is empty, set it at first.");
        }
        if (count($this->errors)) {
            $this->setStatusError();
        } else {
            $this->setStatusOK();
        }
        return $this;
    }

    function setStatusError() {
        $this->setStatus(Response::HTTP_BAD_REQUEST);
        return $this;
    }

    function setStatusOK() {
        $this->setStatus(Response::HTTP_OK);
        return $this;
    }

    function isOk($message, $data = null) {
        if ($data) {
            $this->data = $data;
        }
        $this->setStatus(Response::HTTP_OK);
        $this->message = $message;
        return $this;
    }

    public function setMessageAsValidationKey($key = 'error') {
        $this->errors[$key] = $this->getMessage();
        return $this;
    }

    function isError($message) {
        $this->setStatus(Response::HTTP_BAD_REQUEST);
        $this->message = $message;
        return $this;
    }

    function isNotFound($message) {
        return $this->setStatus(Response::HTTP_NOT_FOUND)
            ->setMessage($message);
    }

    function appendToData($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    function removeFromData($key) {
        unset($this->data[$key]);
        return $this;
    }

    function formatErrors() {
        $this->formatErrors = true;
        return $this;
    }

    public function __call($name, $arguments) {
        if (Str::startsWith($name, 'setHttpMethod')) {
            return $this->setHttpMethod(Str::lower(Str::replaceFirst('setHttpMethod', '', $name)));
        }
        if (Str::startsWith($name, 'setUri')) {
            return $this->setUri(Str::kebab(Str::replaceFirst('setUri', '', $name)));
        }
    }

    public function toResponse($request) {
        return $this->build();
    }

    function build() {


        $this->buildPagination();
        $this->returnAsObject();
        $json = [
            'status' => (int)$this->getStatus(),
            'message' => $this->getMessage(),
            'errors' => (object)$this->getErrors(),
            'data' => $this->getData(),
        ];
        $this->appendDebug($json);
        $_json = array_merge($json, $this->getAttributes());
        return response()->json($_json, $this->getStatus());
    }

    private function buildPagination() {
        if ($this->data instanceof AnonymousResourceCollection && $this->data->resource instanceof LengthAwarePaginator) {
            $this->addAttribute('pagination', api_model_set_pagination($this->data->resource));
        }
    }

    function addAttribute(string $attribute, $data) {
        $this->attributes[$attribute] = $data;
        return $this;
    }

    function returnAsObject() {
        $this->returnAsObject = true;
        return $this;
    }

    function getStatus() {
        return $this->status;
    }

    function setStatus(int $status) {
        $this->status = $status;
        return $this;
    }

    function getMessage() {
        return $this->message ?? '';
    }

    function setMessage(string $message) {
        $this->message = $message;
        return $this;
    }

    function getErrors() {
        $errors = $this->errors ?? [];
        $fullErrors = [];
        if ($this->formatErrors === true) {
            foreach ($errors as $key => $message) {
                $fullErrors[] = [
                    'key' => $key,
                    'value' => $message,
                ];
            }
            return $fullErrors;
        }
        if ($this->returnAsObject) {
            return (object)$errors;
        }
        return $errors;
    }

    function setErrors(array $errors) {
        $this->errors = $errors;
        return $this;
    }

    function getData() {
        if ($this->returnAsObject) {
            return (object)($this->data ?? []);
        }
        return $this->data ?? [];
    }

    function setData($data) {
        $this->data = $data;
        return $this;
    }

    function getAttributes() {
        return $this->attributes ?? [];
    }

}
