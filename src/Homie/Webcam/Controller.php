<?php

namespace Homie\Webcam;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use League\Flysystem\Filesystem as RemoteFilesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ControllerAnnotation("WebcamController")
 */
class Controller
{
    use EventDispatcherTrait;
    use IdGeneratorTrait;

    /**
     * @var Webcam
     */
    private $webcam;

    /**
     * @var RemoteFilesystem
     */
    private $filesystem;

    /**
     * @Inject({"@Webcam", "@RemoteFilesystem"})
     * @param Webcam $webcam
     * @param RemoteFilesystem $filesystem
     */
    public function __construct(Webcam $webcam, RemoteFilesystem $filesystem)
    {
        $this->webcam     = $webcam;
        $this->filesystem = $filesystem;
    }

    /**
     * @return array
     * @Route("/webcam/", name="webcam.index", methods="GET")
     */
    public function index()
    {
        $files = $this->webcam->getFiles();

        return [
            'files' => $files
        ];
    }

    /**
     * @return array
     * @Route("/webcam/recent/", name="webcam.recent", methods="GET")
     */
    public function loadRecent()
    {
        return $this->webcam->getRecentImage();
    }

    /**
     * @param Request $request
     * @param string $type
     * @return bool
     * @Route("/webcam/{type}/", name="webcam.take", methods="POST")
     */
    public function take(Request $request, $type)
    {
        $name = $this->generateRandomId();

        $duration = (int)$request->request->get('duration');

        switch($type) {
            case 'video':
                $event = new WebcamEvent($name, WebcamEvent::TAKE_VIDEO, $duration);
                break;
            case 'sound':
                $event = new WebcamEvent($name, WebcamEvent::TAKE_SOUND, $duration);
                break;
            default:
                $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);

        }
        $this->dispatchInBackground($event);

        return true;
    }

    /**
     * @Route("/webcam/", name="webcam.delete", methods="DELETE")
     * @param Request $request
     * @return boolean
     */
    public function delete(Request $request)
    {
        $filename = $request->query->get('shotId');

        $this->webcam->delete($filename);

        return true;
    }

    /**
     * @Route("/webcam/file/", name="webcam.getFile")
     * @param Request $request
     * @return Response
     */
    public function getFile(Request $request)
    {
        $file   = $request->query->get('file');
        $stream = $this->filesystem->readStream($file);
        $mime   = $this->filesystem->getMimetype($file);

        $response = new Response();
        $response->setContent(stream_get_contents($stream));
        $response->headers->set('Content-Type', $mime);
        $response->headers->set('Cache-Control', 'max-age=86400, must-revalidate');

        return $response;
    }
}
