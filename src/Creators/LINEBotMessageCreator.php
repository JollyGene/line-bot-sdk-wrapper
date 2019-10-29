<?php

namespace JollyGene\Creators;

use JollyGene\Utils\LINEBotUtil;
use JollyGene\Creators\LINEBotTemplateCreator;
use JollyGene\Creators\LINEBotActionCreator;
use JollyGene\Creators\LINEBotBubbleCreator;
use Illuminate\Validation\Rule;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;

class LINEBotMessageCreator
{

    private $util;
    private $templateCreator;
    private $actionCreator;
    private $bubbleCreator;

    public function __construct(
        LINEBotUtil $linebotUtil,
        LINEBotTemplateCreator $linebotTemplateCreator,
        LINEBotActionCreator $linebotActionCreator,
        LINEBotBubbleCreator $linebotBubbleCreator
    )
    {
        $this->util = $linebotUtil;
        $this->templateCreator = $linebotTemplateCreator;
        $this->actionCreator = $linebotActionCreator;
        $this->bubbleCreator = $linebotBubbleCreator;
    }

    /**
     * Reply Message
     *
     * @param LINEBot $bot
     * @param string $replyToken
     * @param MessageBuilder $message
     * @return void
     */
    public function replyMessage($bot, $replyToken, $message)
    {
        $response = $bot->replyMessage($replyToken, $message);
        if (!$response->isSucceeded()) {
            throw new \Exception($response->getRawBody());
        }
    }

    /**
     * Create message from array config.
     *
     * @param array $config
     * @return MessageBuilder
     */
    public function createMessage($props)
    {
        $this->util->validate($props, [
            'type' => 'required',
        ]);

        $message = null;
        switch ($props['type']) {
            case 'text':
                $message = $this->createTextMessage($props);
                break;
            case 'sticker':
                $message = $this->createStickerMessage($props);
                break;
            case 'image':
                $message = $this->createImageMessage($props);
                break;
            case 'video':
                $message = $this->createVideoMessage($props);
                break;
            case 'audio':
                $message = $this->createAudioMessage($props);
                break;
            case 'location':
                $message = $this->createLocationMessage($props);
                break;
            case 'imagemap':
                $message = $this->createImagemapMessage($props);
                break;
            case 'template':
                $message = $this->createTemplateMessage($props);
                break;
            case 'flex':
                $message = $this->createFlexMessage($props);
                break;
            default:
                throw new \Exception('Invalid message type.');
                break;
        }
        return $message;
    }

    /**
     * Create message from json config.
     *
     * @param string $json
     * @return MessageBuilder
     */
    public function createMessageFromJson($json)
    {
        $props = json_decode($json, true);
        return $this->createMessage($props);
    }

    /**
     * Create multimessages from array config.
     *
     * @param array $config
     * @return MultiMessageBuilder
     */
    public function createMessages($config)
    {
        $multiMessage = new MessageBuilder\MultiMessageBuilder();

        foreach ($config as $props) {
            $multiMessage->add($this->createMessage($props));
        }
        return $multiMessage;
    }

    /**
     * Create multimessages from json config.
     *
     * @param string $json
     * @return MultiMessageBuilder
     */
    public function createMessagesFromJson($json)
    {
        $props = json_decode($json, true);
        return $this->createMessages($props);
    }

    /**
     * Create text message.
     *
     * @param array $props
     * @return TextMessageBuilder
     */
    public function createTextMessage($props)
    {
        $this->util->validate($props, [
            'text' => 'required',
        ]);

        return new MessageBuilder\TextMessageBuilder($props['text']);
    }

    /**
     * Create sticker message
     *
     * @param array $props
     * @return StickerMessageBuilder
     */
    public function createStickerMessage($props)
    {
        $this->util->validate($props, [
            'packageId' => 'required|string',
            'stickerId' => 'required|string',
        ]);

        return new MessageBuilder\StickerMessageBuilder($props['packageId'], $props['stickerId']);
    }

    /**
     * Create image message
     *
     * @param array $props
     * @return ImageMessageBuilder
     */
    public function createImageMessage($props)
    {
        $this->util->validate($props, [
            'originalContentUrl' => ['required', 'string', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
            'previewImageUrl' => ['required', 'string', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
        ]);

        return new MessageBuilder\ImageMessageBuilder($props['originalContentUrl'], $props['previewImageUrl']);
    }

    /**
     * Create video message
     *
     * @param array $props
     * @return VideoMessageBuilder
     */
    public function createVideoMessage($props)
    {
        $this->util->validate($props, [
            'originalContentUrl' => ['required', 'string', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
            'previewImageUrl' => ['required', 'string', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
        ]);

        return new MessageBuilder\VideoMessageBuilder($props['originalContentUrl'], $props['previewImageUrl']);
    }

    /**
     * Create audio message
     *
     * @param array $props
     * @return AudioMessageBuilder
     */
    public function createAudioMessage($props)
    {
        $this->util->validate($props, [
            'originalContentUrl' => ['required', 'string', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
            'duration' => 'required|integer',
        ]);

        return new MessageBuilder\AudioMessageBuilder($props['originalContentUrl'], $props['duration']);
    }

    /**
     * Create location message
     *
     * @param array $props
     * @return LocationMessageBuilder
     */
    public function createLocationMessage($props)
    {
        $this->util->validate($props, [
            'title' => 'required|string|max:100',
            'address' => 'required|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        return new MessageBuilder\LocationMessageBuilder($props['title'], $props['address'], $props['latitude'], $props['longitude']);
    }

    /**
     * Create imagemap message
     *
     * @param array $props
     * @return ImagemapMessageBuilder
     */
    public function createImagemapMessage($props)
    {
        $video = NULL;
        $this->util->validate($props, [
            'baseUrl' => ['required', 'string', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
            'altText' => 'required|string|max:400',
            'baseSize' => 'required|array',
            'baseSize.width' => ['required', 'integer', Rule::in([1040])],
            'baseSize.height' => 'required|integer',
            'video' => 'array',
            'video.originalContentUrl' => ['string', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
            'video.previewImageUrl' => ['string', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
            'video.area' => 'array',
            'video.area.x	' => 'integer',
            'video.area.y' => 'integer',
            'video.area.width' => 'integer',
            'video.area.height' => 'integer',
            'video.externalLink' => 'array',
            'video.externalLink.linkUri' => ['max:1000', 'regex:/^(http|https|line|tel):\/\/([A-Z0-9][A-Z0-9_-]*)?/i'],
            'video.externalLink.label' => 'string|max:30',
            'actions' => 'required|array|max:50',
            'actions.*.type' => ['required', Rule::in('uri', 'message')],
        ]);

        $actions = [];
        foreach ($props['actions'] as $actionProp) {
            switch ($actionProp['type']) {
                case 'uri':
                    $actions[] = $this->actionCreator->createImagemapUriAction($actionProp);
                    break;
                case 'message':
                    $actions[] = $this->actionCreator->createImagemapMessageAction($actionProp);
                    break;
                default:
                    break;
            }
        }

        if (isset($props['video'])) {
            $videoProp = $props['video'];
            $externalLink = NULL;
            if (isset($videoProp['externalLink'])) {
                $externalLink = new MessageBuilder\Imagemap\ExternalLinkBuilder($videoProp['externalLink']['linkUri'], $videoProp['externalLink']['label']);
            }
            $video = new MessageBuilder\Imagemap\VideoBuilder(
                $videoProp['originalContentUrl'],
                $videoProp['previewImageUrl'],
                new ImagemapActionBuilder\AreaBuilder($videoProp['area']['x'], $videoProp['area']['y'], $videoProp['area']['width'], $videoProp['area']['height']),
                $externalLink
            );
        }
        return new MessageBuilder\ImagemapMessageBuilder(
            $props['baseUrl'],
            $props['altText'],
            new MessageBuilder\Imagemap\BaseSizeBuilder($props['baseSize']['height'], $props['baseSize']['width']),
            $actions,
            NULL,
            $video
        );
    }

    /**
     * Create template message
     *
     * @param array $props
     * @return TemplateMessageBuilder
     */
    public function createTemplateMessage($props)
    {
        $this->util->validate($props, [
            'altText' => 'required|max:400',
            'template' => 'required|array',
            'template.type' => ['required', Rule::in(['confirm', 'buttons', 'carousel', 'image_carousel'])],
        ]);

        switch ($props['template']['type']) {
            case 'confirm':
                $template = $this->templateCreator->createConfirmTemplate($props['template']);
                break;
            case 'buttons':
                $template = $this->templateCreator->createButtonTemplate($props['template']);
                break;
            case 'carousel':
                $template = $this->templateCreator->createCarouselTemplate($props['template']);
                break;
            case 'image_carousel':
                $template = $this->templateCreator->createImageCarouselTemplate($props['template']);
                break;
            default:
                break;
        }

        $message = new MessageBuilder\TemplateMessageBuilder($props['altText'], $template);
        return $message;
    }

    /**
     * create flex message
     *
     * @param array $props
     * @return FlexMessageBuilder
     */
    public function createFlexMessage($props)
    {
        $this->util->validate($props, [
            'altText' => 'required|max:400',
            'contents' => 'required|array',
            'contents.type' => ['required', Rule::in(['bubble', 'carousel'])],
        ]);

        $contents = NULL;
        switch ($props['contents']['type']) {
            case 'bubble':
                $contents = $this->bubbleCreator->createBubbleContainer($props['contents']);
                break;
            case 'carousel':
                $bubbles = [];
                foreach ($props['contents']['contents'] as $bubbleProps) {
                    $bubbles[] = $this->bubbleCreator->createBubbleContainer($bubbleProps);
                    $contents = new MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder($bubbles);
                }
                break;
            default:
                break;
        }

        return new MessageBuilder\FlexMessageBuilder($props['altText'], $contents);
    }
}