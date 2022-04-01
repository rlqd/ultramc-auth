<?php

namespace Lib;


class Controller
{
    use TSingleton;

    protected const JSON_FLAGS = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR;

    /**
     * @param IAction $action
     * @throws Exception
     */
    public function run(IAction $action) : void
    {
        $result = $action->call();
        if ($result !== null) {
            echo $this->encode($result);
        }
    }

    public function encode(array $data) : string
    {
        return json_encode($data, self::JSON_FLAGS);
    }

    public function handleError(\Throwable $t) : void
    {
        [$code, $message] = ErrorHandler::process($t);
        http_response_code($code);
        if ($t instanceof IHeaderContainer) {
            foreach ($t->getHeaders() as $name => $value) {
                header("$name: $value");
            }
        }
        die(htmlspecialchars($message, ENT_NOQUOTES));
    }
}
