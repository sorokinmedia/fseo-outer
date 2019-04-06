<?php

namespace FseoOuter\api\models;

/**
 * Class RestMessage
 * @package FseoOuter\api\models
 */
class RestMessage
{
    const TYPE_SUCCESS = 0;
    const TYPE_VALIDATION_ERROR = 1;
    const TYPE_SERVER_ERROR = 2;
    const TYPE_LOG = 3;
    const TYPE_INFO = 4;

    public $type;
    public $message;
    public $targetField;

    /**
     * RestMessage constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->type = $params['type'];
        $this->message = $params['message'];
        $this->targetField = $params['targetField'];
        return $this;
    }
}
