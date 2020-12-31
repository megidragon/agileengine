<?php
namespace ImporterModule\Exceptions;

use Exception;

class InvalidTokenException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request
     * @return void
     * @throws Exception
     */
    public function render($request)
    {
        throw new Exception('Invalid token sended to the server', 401);
    }
}
