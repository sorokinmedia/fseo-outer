<?php

namespace FseoOuter\api\models;

/**
 * Class ApiAnswer
 * @package FseoOuter\api\models
 */
class ApiAnswer
{
    const STATUS_SUCCESS = 0;
    const STATUS_LOG = 10;
    const STATUS_ERROR = 100;

    public $response;
    public $messages;
    public $status;

    /**
     * ApiAnswer constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->response = $params['response'];
        $this->messages = $params['messages'];
        $this->status = $params['status'];
        return $this;
    }
}
