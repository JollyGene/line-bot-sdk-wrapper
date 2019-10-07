<?php

namespace JollyGene\Creators;

use JollyGene\Utils\LINEBotUtil;
use JollyGene\Creators\LINEBotActionCreator;
use Illuminate\Validation\Rule;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;

class LINEBotTemplateCreator
{
    private $util;
    private $actionCreator;

    public function __construct(
        LINEBotUtil $linebotUtil,
        LINEBotActionCreator $linebotActionCreator
    )
    {
        $this->util = $linebotUtil;
        $this->actionCreator = $linebotActionCreator;
    }

    /**
     * Create confirm template
     *
     * @param array $props
     * @return ConfirmTemplateBuilder
     */
    public function createConfirmTemplate($props)
    {
        $this->util->validate($props, [
            'text' => 'required|max:240',
            'actions' => 'required|array|min:2|max:2',
        ]);
        $actions = [];
        foreach ($props['actions'] as $actionProp) {
            $actions[] = $this->actionCreator->createAction($actionProp);
        }
        return new TemplateBuilder\ConfirmTemplateBuilder($props['text'], $actions);
    }

    /**
     * Create button template
     *
     * @param Array $props
     * @return ButtonTemplateBuilder
     */
    public function createButtonTemplate($props)
    {
        $thumbnailImageUrl = NULL;
        $imageAspectRatio = 'rectangle';
        $imageSize = 'cover';
        $imageBackgroundColor = '#FFFFFF';
        $title = NULL;
        $defaultAction = NULL;

        $this->util->validate($props, [
            'thumbnailImageUrl' => ['string', 'max|1000', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
            'imageAspectRatio' => Rule::in(['rectangle', 'square']),
            'imageSize' => Rule::in(['cover', 'contain']),
            'imageBackgroundColor' => 'string',
            'title' => 'string|max:40',
            'text' => 'required|max:160',
            'defaultAction' => 'array',
            'actions' => 'required|array|max:4',
        ]);

        if (isset($props['thumbnailImageUrl'])) {
            $thumbnailImageUrl = $props['thumbnailImageUrl'];
        }
        if (isset($props['imageAspectRatio'])) {
            $imageAspectRatio = $props['imageAspectRatio'];
        }
        if (isset($props['imageSize'])) {
            $imageSize = $props['imageSize'];
        }
        if (isset($props['imageBackgroundColor'])) {
            $imageBackgroundColor = $props['imageBackgroundColor'];
        }
        if (isset($props['title'])) {
            $title = $props['title'];
        }
        if (isset($props['defaultAction'])) {
            $defaultAction = $this->actionCreator->createAction($props['defaultAction']);;
        }

        $actions = [];
        foreach ($props['actions'] as $actionProp) {
            $actions[] = $this->actionCreator->createAction($actionProp);
        }

        return new TemplateBuilder\ButtonTemplateBuilder($title, $props['text'], $thumbnailImageUrl, $actions, $imageAspectRatio, $imageSize, $imageBackgroundColor, $defaultAction);
    }

    /**
     * Create carousel template
     *
     * @param array $props
     * @return CarouselTemplateBuilder
     */
    public function createCarouselTemplate($props)
    {
        $imageAspectRatio = 'rectangle';
        $imageSize = 'cover';
        $this->util->validate($props, [
            'text' => 'required|max:240',
            'columns' => 'required|array|max:10',
            'imageAspectRatio' => Rule::in(['rectangle', 'square']),
            'imageSize' => Rule::in(['cover', 'contain']),
        ]);

        if (isset($props['imageAspectRatio'])) {
            $imageAspectRatio = $props['imageAspectRatio'];
        }
        if (isset($props['imageSize'])) {
            $imageSize = $props['imageSize'];
        }

        $columns = [];
        foreach ($props['columns'] as $columnProp) {
            $columns[] = $this->createCarouselColumnTemplate($columnProp);
        }
        return new TemplateBuilder\CarouselTemplateBuilder($columns, $imageAspectRatio, $imageSize);
    }

    /**
     * Create image carousel template
     *
     * @param array $props
     * @return ImageCarouselTemplateBuilder
     */
    public function createImageCarouselTemplate($props)
    {
        $this->util->validate($props, [
            'columns' => 'required|array|max:10',
        ]);

        $columns = [];
        foreach ($props['columns'] as $columnProp) {
            $columns[] = $this->createImageCarouselColumnTemplate($columnProp);
        }
        return new TemplateBuilder\ImageCarouselTemplateBuilder($columns);
    }

    /**
     * create carousel column template
     *
     * @param array $props
     * @return CarouselColumnTemplateBuilder
     */
    public function createCarouselColumnTemplate($props)
    {
        $thumbnailImageUrl = NULL;
        $imageBackgroundColor = '#FFFFFF';
        $title = NULL;

        $this->util->validate($props, [
            'thumbnailImageUrl' => ['string', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
            'imageBackgroundColor' => 'string',
            'title' => 'string|max:40',
            'text' => 'required|max:120',
            'actions' => 'required|array|max:3',
        ]);

        if (isset($props['thumbnailImageUrl'])) {
            $thumbnailImageUrl = $props['thumbnailImageUrl'];
        }
        if (isset($props['imageBackgroundColor'])) {
            $imageBackgroundColor = $props['imageBackgroundColor'];
        }
        if (isset($props['title'])) {
            $title = $props['title'];
        }

        $actions = [];
        foreach ($props['actions'] as $actionProp) {
            $actions[] = $this->actionCreator->createAction($actionProp);
        }

        return new TemplateBuilder\CarouselColumnTemplateBuilder($title, $props['text'], $thumbnailImageUrl, $actions, $imageBackgroundColor);
    }

    /**
     * create image carousel column template
     *
     * @param array $props
     * @return ImageCarouselColumnTemplateBuilder
     */
    public function createImageCarouselColumnTemplate($props)
    {
        $this->util->validate($props, [
            'thumbnailImageUrl' => ['required', 'string', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
            'action' => 'required|array',
        ]);

        $action = $this->actionCreator->createAction($props['action']);

        return new TemplateBuilder\ImageCarouselColumnTemplateBuilder($props['thumbnailImageUrl'], $action);
    }
}