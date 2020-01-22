<?php
/**
 * Created by PhpStorm.
 * User: thomaslarousse
 * Date: 06/01/2020
 * Time: 15:04
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;

class CacheHandler
{
    public $response;

    /**
     * Start Cache and make it public
     */
    public function startCache($response)
    {
        $this->response = $response;
        $this->response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $this->response->setPublic(); // make sure the response is public/cacheable

        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function setEtag($content)
    {
        $this->response->setEtag(md5($content));

        return $this;
    }

    /**
     * @param int $time
     * @return $this
     */
    public function setSharedMaxAge(int $time)
    {
        $this->response->setSharedMaxAge($time);

        return $this;
    }

}