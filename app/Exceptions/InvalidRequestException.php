<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InvalidRequestException extends Exception
{
    // 构造函数，允许设置错误消息和HTTP状态码
    public function __construct(string $message = "", int $code = 400)
    {
        parent::__construct($message, $code);
    }

    // $message、$code 是可以直接通过$this->message/code访问到的
    public function render(Request $request) {
        if($request ->exceptsJson()) {
            // json() 方法第二个参数就是 http 返回码
            return response()->json(['msg' => $this.message], $this->code);
        }

        return view('pages.error', ['msg' => $this.message]);
    }
}
