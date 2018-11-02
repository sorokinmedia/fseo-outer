<?php
namespace FseoOuter\api\models;

class ApiAnswer
{
    const STATUS_SUCCESS = 0;
    const STATUS_LOG = 10;
    const STATUS_ERROR = 100;

    public $response = null;
    public $messages;
    public $status;

    public function __construct(array $params)
    {
        $this->response = $params['response'];
        $this->messages = $params['messages'];
        $this->status = $params['status'];
        return $this;
    }
}