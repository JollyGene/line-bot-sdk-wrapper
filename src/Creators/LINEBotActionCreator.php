<?php

namespace JollyGene\Creators;

use JollyGene\Utils\LINEBotUtil;
use Illuminate\Validation\Rule;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder;

class LINEBotActionCreator
{
    private $util;

    public function __construct(LINEBotUtil $linebotUtil)
    {
        $this->util = $linebotUtil;
    }

    /**
     * Create action
     *
     * @param array $props
     * @return TemplateActionBuilder
     */
    public function createAction($props)
    {
        $this->util->validate($props, [
            'type' => ['required', Rule::in(['message', 'postback', 'uri', 'datetimepicker', 'camera', 'cameraRoll', 'location'])],
        ]);

        $action = NULL;
        switch ($props['type']) {
            case 'postback':
                $action = $this->createPostbackAction($props);
                break;
            case 'message':
                $action = $this->createMessageAction($props);
                break;
            case 'uri':
                $action = $this->createUriAction($props);
                break;
            case 'datetimepicker':
                $action = $this->createDatetimePickerAction($props);
                break;
            case 'camera':
                $action = $this->createCameraAction($props);
                break;
            case 'cameraRoll':
                $action = $this->createCameraRollAction($props);
                break;
            case 'location':
                $action = $this->createLocationAction($props);
                break;
            default:
                break;
        }
        return $action;
    }

    /**
     * Create postback action
     *
     * @param array $props
     * @return PostbackTemplateActionBuilder
     */
    public function createPostbackAction($props)
    {
        $label = '';
        $this->util->validate($props, [
            'label' => 'max:20',
            'data' => 'required|max:300',
            'displayText' => 'required|max:300',
        ]);
        if (isset($props['label'])) {
            $label = $props['label'];
        }
        return new TemplateActionBuilder\PostbackTemplateActionBuilder($label, $props['data'], $props['displayText']);
    }

    /**
     * Create message action
     *
     * @param array $props
     * @return MessageTemplateActionBuilder
     */
    public function createMessageAction($props)
    {
        $label = '';
        $this->util->validate($props, [
            'label' => 'max:20',
            'text' => 'required|max:300',
        ]);
        if (isset($props['label'])) {
            $label = $props['label'];
        }
        return new TemplateActionBuilder\MessageTemplateActionBuilder($label, $props['text']);
    }

    /**
     * Create uri action
     *
     * @param array $props
     * @return UriTemplateActionBuilder
     */
    public function createUriAction($props)
    {
        $altUri = NULL;
        $this->util->validate($props, [
            'label' => 'max:20',
            'uri' => ['required', 'string', 'max:1000', 'regex:/^(http|https|line|tel):\/\/([A-Z0-9][A-Z0-9_-]*)?/i'],
            'altUri.desktop' => ['string', 'max:1000', 'regex:/^(http|https|line|tel):\/\/([A-Z0-9][A-Z0-9_-]*)?/i'],
        ]);
        $label = substr($props['uri'], 0, 20);
        if (isset($props['label'])) {
            $label = $props['label'];
        }
        if (isset($props['altUri.desktop'])) {
            $altUri = new TemplateActionBuilder\Uri\AltUriBuilder($props['altUri.desktop']);
        }
        return new TemplateActionBuilder\UriTemplateActionBuilder($label, $props['uri'], $altUri);
    }

    /**
     * Create datetimepicker action
     *
     * @param array $props
     * @return DatetimePickerTemplateActionBuilder
     */
    public function createDatetimePickerAction($props)
    {
        $label = '';
        $initial = NULL;
        $max = NULL;
        $min = NULL;
        $this->util->validate($props, [
            'label' => 'max:20',
            'data' => 'required|max:300',
            'mode' => ['required', Rule::in(['date', 'time', 'datetime'])],
            'initial' => 'date',
            'max' => 'date',
            'min' => 'date',
        ]);
        if (isset($props['label'])) {
            $label = $props['label'];
        }
        if (isset($props['initial'])) {
            $initial = $props['initial'];
        }
        if (isset($props['max'])) {
            $max = $props['max'];
        }
        if (isset($props['min'])) {
            $min = $props['min'];
        }
        return new TemplateActionBuilder\DatetimePickerTemplateActionBuilder($label, $props['data'], $props['mode'], $initial, $max, $min);
    }

    /**
     * Create camera action
     *
     * @param array $props
     * @return CameraTemplateActionBuilder
     */
    public function createCameraAction($props)
    {
        $label = '';
        $altUri = NULL;
        $this->util->validate($props, [
            'label' => 'max:20',
        ]);
        if (isset($props['label'])) {
            $label = $props['label'];
        }
        return new TemplateActionBuilder\CameraTemplateActionBuilder($label);
    }

    /**
     * Create camera roll action
     *
     * @param array $props
     * @return CameraRollTemplateActionBuilder
     */
    public function createCameraRollAction($props)
    {
        $label = '';
        $altUri = NULL;
        $this->util->validate($props, [
            'label' => 'max:20',
        ]);
        if (isset($props['label'])) {
            $label = $props['label'];
        }
        return new TemplateActionBuilder\CameraRollTemplateActionBuilder($label);
    }

    /**
     * Create map action
     *
     * @param array $props
     * @return LocationTemplateActionBuilder
     */
    public function createLocationAction($props)
    {
        $label = '';
        $altUri = NULL;
        $this->util->validate($props, [
            'label' => 'max:20',
        ]);
        if (isset($props['label'])) {
            $label = $props['label'];
        }
        return new TemplateActionBuilder\LocationTemplateActionBuilder($label);
    }

    /**
     * Create imagemap uri action
     *
     * @param array $props
     * @return ImagemapUriActionBuilder
     */
    public function createImagemapUriAction($props)
    {
        $this->util->validate($props, [
            'linkUri' => ['required', 'string', 'max:1000', 'regex:/^(http|https|line|tel):\/\/([A-Z0-9][A-Z0-9_-]*)?/i'],
            'area' => 'required|array',
            'area.x' => 'required|integer',
            'area.y' => 'required|integer',
            'area.width' => 'required|integer',
            'area.height' => 'required|integer',
        ]);
        $areaProps = $props['area'];
        return new ImagemapActionBuilder\ImagemapUriActionBuilder(
            $props['linkUri'],
            new ImagemapActionBuilder\AreaBuilder($areaProps['x'], $areaProps['y'], $areaProps['width'], $areaProps['height'])
        );
    }

    /**
     * Create imagemap message action
     *
     * @param array $props
     * @return ImagemapMessageActionBuilder
     */
    public function createImagemapMessageAction($props)
    {
        $this->util->validate($props, [
            'text' => 'required|max:400',
            'area' => 'required|array',
            'area.x' => 'required|integer',
            'area.y' => 'required|integer',
            'area.width' => 'required|integer',
            'area.height' => 'required|integer',
        ]);
        $areaProps = $props['area'];
        return new ImagemapActionBuilder\ImagemapMessageActionBuilder(
            $props['text'],
            new ImagemapActionBuilder\AreaBuilder($areaProps['x'], $areaProps['y'], $areaProps['width'], $areaProps['height'])
        );
    }
}