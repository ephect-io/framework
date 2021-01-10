<?php
namespace Ephel\Web;

/**
 * Description of httpTransport
 *
 * @author David
 */
interface HttpTransportInterface
{
    public function getRequest();
    public function getResponse();
}
