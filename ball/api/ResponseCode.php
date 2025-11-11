<?php

namespace ball\api;


class ResponseCode
{
    const SUCCESS = '000';
    // invalid channel token or secret
    const ERROR_CHANNEL = '101';
    // lack parameters
    const ERROR_LACK_PARAMS = '102';
    // server 500
    const ERROR_INTERNAL = '103';
    // process failed
    const ERROR_FAILED = '104';
    // not exists error
    const ERROR_NON_EXISTS = '105';
    // need login
    const ERROR_NEED_LOGIN = '106';
    // vote exists
    const ERROR_VOTE_EXISTS = '107';
    // vote exists
    const ERROR_VOTE_EXISTS_DAILY = '108';

    public static function errors(string $code, string $message)
    {
        return [
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ];
    }

    public static function success(string $message = 'success')
    {
        return [
            'code' => static::SUCCESS,
            'message' => $message
        ];
    }
}
