<?php
namespace Swiftriver\Core\Modules\SiSW;
class ServiceWrapper {

    /**
     * The Uri of the service
     * @var string
     */
    private $uri;

    /**
     * Constructor Method
     * @param string $uri
     */
    public function __construct($uri) {
        $this->uri = $uri;
    }

    /**
     *
     * @param array $postData
     * @param int $timeout
     * @return string
     */
    public function MakePOSTRequest($postData, $timeout) {
        $context = stream_context_create(
            array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query($postData),
                    'timeout' => $timeout,
                ),
            ));
        $returnData = file_get_contents($this->uri, false, $context);
        return $returnData;
    }

    /**
     *
     * @param string $json
     * @param int $timeout
     * @return string
     */
    public function MakeJSONPOSTRequest($json, $timeout) {
        $context = stream_context_create(
            array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/json; charset=utf-8\r\n',
                    'content' => $json,
                    'timeout' => $timeout,
                ),
            ));
        $returnData = file_get_contents($this->uri, false, $context);
        return $returnData;
    }

    /**
     *
     */
    public function MakeGETRequest() {
        $returnData = file_get_contents($this->uri, false);
        return $returnData;
    }
}
?>
