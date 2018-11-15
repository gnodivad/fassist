<?php

namespace App\Exceptions;

use Exception;

class ApplicationException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => [
                [
                    'status' => 400,
                    'field' => null,
                    'title' => 'Fassist error',
                    'detail' => $this->getMessage()
                ]
            ]
        ]);
    }
}
