<?php
namespace Reed\Web;

use Reed\Auth\Authentication;
use Reed\Web\Request;
use Reed\Web\Response;

/**
 * Description of httpTransport
 *
 * @author David
 */
trait HttpTransportTrait
{
    //put your code here
    protected $request = null;
    protected $response = null;
    protected $authentication = null;

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
