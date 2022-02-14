<?php

class JSONResponse
{
    public int $code = 200;
    public ?string $message = null;

    /**
     * @param int $code
     * @param $object
     */
    public function __construct(int $code, $object)
    {
        $this->code = $code;
        $this->message = json_encode($object);
    }
}