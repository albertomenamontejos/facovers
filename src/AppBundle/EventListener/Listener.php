<?php


namespace AppBundle\EventListener;
use AppBundle\Entity\Post;
//use AppBundle\Model\FileMimeType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Event\Event;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FFMpeg;

class Listener
{
    /**
     * @inheritdoc
     *
     * Executed before file upload
     * We use it to compute md5 file checksum and set thumbnail into entity
     */
    public function onVichUploaderPreUpload(Event $event)
    {
        if($event->getMapping()->getFilePropertyName() === 'thumbnail') {
            return;
        }

        $object = $event->getObject();

        if ($object instanceof FileEntity) {
            $object->setChecksum(md5_file($object->getFile()->getPathname()));
        }

        if ($object instanceof FileEntity && in_array($object->getFile()->getMimeType(), FileEntity::THUMBNAIL_MIMETYPES)) {

            $format = sprintf('%s.png', $object->getFile()->getRealPath());

            if($object->getFile()->getMimeType() === FileMimeType::PDF) {
                $imagick = new \Imagick();
                $imagick->readImage(sprintf('%s[0]', $object->getFile()->getRealPath()));
                $imagick->writeImage($format);

            } else {
                $ffmpeg = FFMpeg::create(array(
                    'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                    'ffprobe.binaries' => '/usr/bin/ffprobe'
                ));
                $video = $ffmpeg->open($object->getFile()->getRealPath());
                $frame = $video->frame(Coordinate\TimeCode::fromSeconds(FileEntity::THUMBNAIL_VIDEO_SECONDS));
                $frame->save($format);
            }

            $file = new File($format);
            $thumbnail = new UploadedFile($file->getPathname(), $file->getFilename(), $file->getMimeType(), $file->getSize());

            $object->setThumbnail($thumbnail);
            $object->setThumbnailName($file->getFilename());
        }
    }
}