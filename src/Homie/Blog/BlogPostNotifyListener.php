<?php

namespace Homie\Blog;

use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\TimeTrait;
use Homie\Blog\Events\BlogEvent;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class BlogPostNotifyListener implements EventSubscriberInterface
{

    const NOTIFY_TIME = '19:55';

    use TimeTrait;
    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
        BlogEvent::POST => 'handlePostEvent'
        ];
    }

    /**
     * @param BlogEvent $event
     */
    public function handlePostEvent(BlogEvent $event)
    {
        $post = $event->post;

        $hour = $this->getTime()->date('G');
        $minute = (int)$this->getTime()->date('i');
        $text = sprintf('%s hat um %d Uhr %d geschrieben: %s.', $event->userVo->username, $hour, $minute, $post->text);

        if ($post->mood) {
            $text .= sprintf(' Stimmung: %d von %d', $post->mood, BlogPostVO::MAX_MOOD);
        }

        $espeak = new EspeakVO($text);
        $event = new EspeakEvent($espeak);

        // speak NOW and today at next defined time
        $this->dispatchInBackground($event);

        $time = $this->getTime()->strtotime(self::NOTIFY_TIME);
        if ($time > $this->now()) {
            $this->dispatchInBackground($event, $time);
        }

    }
}
