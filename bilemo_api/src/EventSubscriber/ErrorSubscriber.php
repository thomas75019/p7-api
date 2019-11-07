<?php
namespace App\EventSubscriber;

use App\Normalizer\NormalizerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ErrorSubscriber implements EventSubscriberInterface
{
    private $serializer;
    private $normalizers;
    private $exception;


    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->exception = \Exception::class;
    }

    public function processException(ExceptionEvent $event)
    {
        $result = null;

        /*foreach ($this->normalizers as $normalizer) {
            if ($normalizer->supports($event->getException())) {
                $result = $normalizer->normalize($event->getException());

                break;
            }
        }*/

        if (null == $result) {
            $result['code'] = Response::HTTP_BAD_REQUEST;

            $result['body'] = [
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $event->getException()->getMessage()
            ];
        }


        $body = $this->serializer->serialize($result['body'], 'json');

        $event->setResponse(new Response($body, $result['code']));
    }

    public function addNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizers[] = $normalizer;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['processException', 255],
            ]
        ];
    }

}