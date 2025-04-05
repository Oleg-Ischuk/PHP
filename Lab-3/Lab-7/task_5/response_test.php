<?php

class Response {
    private $statusCode = 200;
    private $headers = [];

    public function setStatus($code) {
        $this->statusCode = $code;
        return $this;
    }

    public function addHeader($header) {
        $this->headers[] = $header;
        return $this;
    }

    public function send($content) {
        if (ob_get_level()) {
            ob_clean();
        } else {
            ob_start();
        }

        http_response_code($this->statusCode);

        foreach ($this->headers as $header) {
            header($header);
        }

        echo $content;

        ob_end_flush();
    }
}

$response = new Response();
$response->setStatus(200);
$response->addHeader("Content-Type: text/html");
$response->addHeader("Oleg: Test");
$response->send("<h1>Вітаємо!</h1><p>Це динамічна відповідь.</p>");
?>