<?php

namespace JollyGene\Creators;

use JollyGene\Utils\LINEBotUtil;
use JollyGene\Creators\LINEBotActionCreator;
use Illuminate\Validation\Rule;
use LINE\LINEBot\MessageBuilder\Flex;

class LINEBotBubbleCreator
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
     * create bubble container
     *
     * @param array $props
     * @return BubbleContainerBuilder
     */
    public function createBubbleContainer($props)
    {
        $size = NULL;
        $direction = NULL;
        $header = NULL;
        $hero = NULL;
        $body = NULL;
        $footer = NULL;
        $styles = NULL;
        $this->util->validate($props, [
            'size' => [Rule::in(['nano', 'micro', 'kilo', 'mega', 'giga'])],
            'direction' => [Rule::in(['ltr', 'rtl'])],
            'header' => 'array',
            'hero' => 'array',
            'body' => 'array',
            'footer' => 'array',
            'styles' => 'array',
        ]);

        if (isset($props['size'])) {
            $size = $props['size'];
        }
        if (isset($props['direction'])) {
            $direction = $props['direction'];
        }
        if (isset($props['header'])) {
            $header = $this->createBoxComponent($props['header']);
        }
        if (isset($props['hero'])) {
            switch ($props['hero']['type']) {
                case 'box':
                    $hero = $this->createBoxComponent($props['hero']);
                    break;
                case 'image':
                    $hero = $this->createImageComponent($props['hero']);
                    break;
                default:
                    break;
            }
        }
        if (isset($props['body'])) {
            $body = $this->createBoxComponent($props['body']);
        }
        if (isset($props['footer'])) {
            $footer = $this->createBoxComponent($props['footer']);
        }
        if (isset($props['styles'])) {
            $styles = $this->createBubbleStyle($props['styles']);
        }

        return new Flex\ContainerBuilder\BubbleContainerBuilder($direction, $header, $hero, $body, $footer, $styles, $size);
    }

    /**
     * create bubble style
     *
     * @param array $props
     * @return BubbleStylesBuilder
     */
    public function createBubbleStyle($props)
    {
        $header = NULL;
        $hero = NULL;
        $body = NULL;
        $footer = NULL;
        $this->util->validate($props, [
            'header' => 'array',
            'hero' => 'array',
            'body' => 'array',
            'footer' => 'array',
        ]);

        if (isset($props['header'])) {
            $header = $this->createBlockStyle($props['header']);
        }
        if (isset($props['hero'])) {
            $hero = $this->createBlockStyle($props['hero']);
        }
        if (isset($props['body'])) {
            $body = $this->createBlockStyle($props['body']);
        }
        if (isset($props['footer'])) {
            $footer = $this->createBlockStyle($props['footer']);
        }
        return new Flex\BubbleStylesBuilder($header, $hero, $body, $footer);
    }

    /**
     * create block style
     *
     * @param array $props
     * @return BlockStyleBuilder
     */
    public function createBlockStyle($props)
    {
        $backgroundColor = NULL;
        $separator = NULL;
        $separatorColor = NULL;
        $this->util->validate($props, [
            'backgroundColor' => 'string',
            'separator' => 'string',
            'separatorColor' => 'string',
        ]);
        if (isset($props['backgroundColor'])) {
            $backgroundColor = $props['backgroundColor'];
        }
        if (isset($props['separator'])) {
            $separator = $props['separator'];
        }
        if (isset($props['separatorColor'])) {
            $separatorColor = $props['separatorColor'];
        }
        return new Flex\BlockStyleBuilder($backgroundColor, $separator, $separatorColor);
    }

    public function createComponent($props)
    {
        $this->util->validate($props, [
            'type' => ['required', Rule::in(['box', 'text', 'button', 'image', 'icon', 'separator', 'filler', 'spacer'])],
        ]);

        $component = NULL;
        switch ($props['type']) {
            case 'box':
                $component = $this->createBoxComponent($props);
                break;
            case 'text':
                $component = $this->createTextComponent($props);
                break;
            case 'button':
                $component = $this->createButtonComponent($props);
                break;
            case 'image':
                $component = $this->createImageComponent($props);
                break;
            case 'icon':
                $component = $this->createIconComponent($props);
                break;
            case 'separator':
                $component = $this->createSeparatorComponent($props);
                break;
            case 'filler':
                $component = $this->createFillerComponent($props);
                break;
            case 'spacer':
                $component = $this->createSpacerComponent($props);
                break;
            default:
                break;
        }
        return $component;
    }

    /**
     * create box component
     *
     * @param array $props
     * @return BoxComponentBuilder
     */
    public function createBoxComponent($props)
    {
        $flex = NULL;
        $spacing = NULL;
        $margin = NULL;
        $action = NULL;
        $this->util->validate($props, [
            'type' => ['required', Rule::in(['box'])],
            'layout' => ['required', Rule::in(['horizontal', 'vertical', 'baseline'])],
            'contents' => 'required|array',
            'backgroundColor' => 'string',
            'borderColor' => 'string',
            'borderWidth' => 'string',
            'cornerRadius' => 'string',
            'width' => 'string',
            'height' => 'string',
            'flex' => 'integer',
            'spacing' => [Rule::in(['none', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl'])],
            'margin' => [Rule::in(['none', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl'])],
            'paddingAll' => 'string',
            'paddingTop' => 'string',
            'paddingBottom' => 'string',
            'paddingStart' => 'string',
            'paddingEnd' => 'string',
            'position' => [Rule::in(['relative', 'absolute'])],
            'offsetTop' => 'string',
            'offsetBottom' => 'string',
            'offsetStart' => 'string',
            'offsetEnd' => 'string',
            'action' => 'array',
        ]);

        if (isset($props['flex'])) {
            $flex = $props['flex'];
        }
        if (isset($props['spacing'])) {
            $spacing = $props['spacing'];
        }
        if (isset($props['margin'])) {
            $margin = $props['margin'];
        }
        if (isset($props['action'])) {
            $action = $this->actionCreator->createAction($props['action']);
        }
        $contents = [];
        foreach ($props['contents'] as $contentProps) {
            $contents[] = $this->createComponent($contentProps);
        }
        $component = new Flex\ComponentBuilder\BoxComponentBuilder($props['layout'], $contents, $flex, $spacing, $margin, $action);

        if (isset($props['backgroundColor'])) {
            $component->setBackgroundColor($props['backgroundColor']);
        }
        if (isset($props['borderColor'])) {
            $component->setBorderColor($props['borderColor']);
        }
        if (isset($props['borderWidth'])) {
            $component->setBorderWidth($props['borderWidth']);
        }
        if (isset($props['cornerRadius'])) {
            $component->setCornerRadius($props['cornerRadius']);
        }
        if (isset($props['width'])) {
            $component->setWidth($props['width']);
        }
        if (isset($props['height'])) {
            $component->setHeight($props['height']);
        }
        if (isset($props['paddingAll'])) {
            $component->setPaddingAll($props['paddingAll']);
        }
        if (isset($props['paddingTop'])) {
            $component->setPaddingTop($props['paddingTop']);
        }
        if (isset($props['paddingBottom'])) {
            $component->setPaddingBottom($props['paddingBottom']);
        }
        if (isset($props['paddingStart'])) {
            $component->setPaddingStart($props['paddingStart']);
        }
        if (isset($props['paddingEnd'])) {
            $component->setPaddingEnd($props['paddingEnd']);
        }
        if (isset($props['position'])) {
            $component->setPosition($props['position']);
        }
        if (isset($props['offsetTop'])) {
            $component->setOffsetTop($props['offsetTop']);
        }
        if (isset($props['offsetBottom'])) {
            $component->setOffsetBottom($props['offsetBottom']);
        }
        if (isset($props['offsetStart'])) {
            $component->setOffsetStart($props['offsetStart']);
        }
        if (isset($props['offsetEnd'])) {
            $component->setOffsetEnd($props['offsetEnd']);
        }

        return $component;
    }

    /**
     * create text component
     *
     * @param array $props
     * @return TextComponentBuilder
     */
    public function createTextComponent($props)
    {
        $text = '';
        $flex = NULL;
        $margin = NULL;
        $size = NULL;
        $align = NULL;
        $gravity = NULL;
        $wrap = NULL;
        $maxLines = NULL;
        $weight = NULL;
        $color = NULL;
        $action = NULL;
        $this->util->validate($props, [
            'text' => 'string',
            'contents' => 'array',
            'flex' => 'integer',
            'margin' => [Rule::in(['none', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl'])],
            'position' => [Rule::in(['relative', 'absolute'])],
            'offsetTop' => 'string',
            'offsetBottom' => 'string',
            'offsetStart' => 'string',
            'offsetEnd' => 'string',
            'size' => [Rule::in(['xxs', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl', '3xl', '4xl', '5xl'])],
            'align' => [Rule::in(['start', 'end', 'center'])],
            'gravity' => [Rule::in(['top', 'bottom', 'center'])],
            'wrap' => 'boolean',
            'maxLines' => 'integer',
            'weight' => [Rule::in(['regular', 'bold'])],
            'color' => 'string',
            'action' => 'array',
            'style' => [Rule::in(['normal', 'italic'])],
            'decoration' => [Rule::in(['none', 'underline', 'line-through'])],
        ]);

        if (isset($props['text'])) {
            $text = $props['text'];
        }
        if (isset($props['flex'])) {
            $flex = $props['flex'];
        }
        if (isset($props['margin'])) {
            $margin = $props['margin'];
        }
        if (isset($props['size'])) {
            $size = $props['size'];
        }
        if (isset($props['align'])) {
            $align = $props['align'];
        }
        if (isset($props['gravity'])) {
            $gravity = $props['gravity'];
        }
        if (isset($props['wrap'])) {
            $wrap = $props['wrap'];
        }
        if (isset($props['maxLines'])) {
            $maxLines = $props['maxLines'];
        }
        if (isset($props['weight'])) {
            $weight = $props['weight'];
        }
        if (isset($props['color'])) {
            $color = $props['color'];
        }
        if (isset($props['action'])) {
            $action = $this->actionCreator->createAction($props['action']);
        }

        $component = new Flex\ComponentBuilder\TextComponentBuilder($text, $flex, $margin, $size, $align, $gravity, $wrap, $maxLines, $weight, $color, $action);

        if (isset($props['position'])) {
            $component->setPosition($props['position']);
        }
        if (isset($props['offsetTop'])) {
            $component->setOffsetTop($props['offsetTop']);
        }
        if (isset($props['offsetBottom'])) {
            $component->setOffsetBottom($props['offsetBottom']);
        }
        if (isset($props['offsetStart'])) {
            $component->setOffsetStart($props['offsetStart']);
        }
        if (isset($props['offsetEnd'])) {
            $component->setOffsetEnd($props['offsetEnd']);
        }
        if (isset($props['style'])) {
            $component->setStyle($props['style']);
        }
        if (isset($props['decoration'])) {
            $component->setDecoration($props['decoration']);
        }
        if (isset($props['contents'])) {
            $component->setText($props['contents'][0]['text']);

            $contents = [];
            foreach ($props['contents'] as $contentProps) {
                $contents[] = $this->createSpanComponent($contentProps);
            }
            $component->setContents($contents);
        }

        return $component;
    }

    /**
     * create button component
     *
     * @param array $props
     * @return ButtonComponentBuilder
     */
    public function createButtonComponent($props)
    {
        $flex = NULL;
        $margin = NULL;
        $height = NULL;
        $style = NULL;
        $color = NULL;
        $gravity = NULL;
        $this->util->validate($props, [
            'action' => 'required|array',
            'flex' => 'integer',
            'margin' => [Rule::in(['none', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl'])],
            'position' => [Rule::in(['relative', 'absolute'])],
            'offsetTop' => 'string',
            'offsetBottom' => 'string',
            'offsetStart' => 'string',
            'offsetEnd' => 'string',
            'height' => [Rule::in(['sm', 'md'])],
            'style' => [Rule::in(['primary', 'secondary', 'link'])],
            'color' => 'string',
            'gravity' => [Rule::in(['top', 'bottom', 'center'])],
        ]);

        $action = $this->actionCreator->createAction($props['action']);
        if (isset($props['flex'])) {
            $flex = $props['flex'];
        }
        if (isset($props['margin'])) {
            $margin = $props['margin'];
        }
        if (isset($props['height'])) {
            $height = $props['height'];
        }
        if (isset($props['style'])) {
            $style = $props['style'];
        }
        if (isset($props['color'])) {
            $color = $props['color'];
        }
        if (isset($props['gravity'])) {
            $gravity = $props['gravity'];
        }

        $component = new Flex\ComponentBuilder\ButtonComponentBuilder($action, $flex, $margin, $height, $style, $color, $gravity);

        if (isset($props['position'])) {
            $component->setPosition($props['position']);
        }
        if (isset($props['offsetTop'])) {
            $component->setOffsetTop($props['offsetTop']);
        }
        if (isset($props['offsetBottom'])) {
            $component->setOffsetBottom($props['offsetBottom']);
        }
        if (isset($props['offsetStart'])) {
            $component->setOffsetStart($props['offsetStart']);
        }
        if (isset($props['offsetEnd'])) {
            $component->setOffsetEnd($props['offsetEnd']);
        }

        return $component;
    }

    /**
     * create image component
     *
     * @param array $props
     * @return ImageComponentBuilder
     */
    public function createImageComponent($props)
    {
        $flex = NULL;
        $margin = NULL;
        $align = NULL;
        $gravity = NULL;
        $size = NULL;
        $aspectRatio = NULL;
        $aspectMode = NULL;
        $backgroundColor = NULL;
        $action = NULL;
        $this->util->validate($props, [
            'url' => ['required', 'string', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
            'flex' => 'integer',
            'margin' => [Rule::in(['none', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl'])],
            'position' => [Rule::in(['relative', 'absolute'])],
            'offsetTop' => 'string',
            'offsetBottom' => 'string',
            'offsetStart' => 'string',
            'offsetEnd' => 'string',
            'align' => [Rule::in(['start', 'end', 'center'])],
            'gravity' => [Rule::in(['top', 'bottom', 'center'])],
            'size' => [Rule::in(['xxs', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl', '3xl', '4xl', '5xl', 'full'])],
            'aspectRatio' => 'string',
            'aspectMode' => [Rule::in(['cover', 'fit'])],
            'backgroundColor' => 'string',
            'action' => 'array',
        ]);

        if (isset($props['flex'])) {
            $flex = $props['flex'];
        }
        if (isset($props['margin'])) {
            $margin = $props['margin'];
        }
        if (isset($props['align'])) {
            $align = $props['align'];
        }
        if (isset($props['gravity'])) {
            $gravity = $props['gravity'];
        }
        if (isset($props['size'])) {
            $size = $props['size'];
        }
        if (isset($props['aspectRatio'])) {
            $aspectRatio = $props['aspectRatio'];
        }
        if (isset($props['aspectMode'])) {
            $aspectMode = $props['aspectMode'];
        }
        if (isset($props['backgroundColor'])) {
            $backgroundColor = $props['backgroundColor'];
        }
        if (isset($props['action'])) {
            $action = $this->actionCreator->createAction($props['action']);
        }

        $component = new Flex\ComponentBuilder\ImageComponentBuilder($props['url'], $flex, $margin, $align, $gravity, $size, $aspectRatio, $aspectMode, $backgroundColor, $action);

        if (isset($props['position'])) {
            $component->setPosition($props['position']);
        }
        if (isset($props['offsetTop'])) {
            $component->setOffsetTop($props['offsetTop']);
        }
        if (isset($props['offsetBottom'])) {
            $component->setOffsetBottom($props['offsetBottom']);
        }
        if (isset($props['offsetStart'])) {
            $component->setOffsetStart($props['offsetStart']);
        }
        if (isset($props['offsetEnd'])) {
            $component->setOffsetEnd($props['offsetEnd']);
        }

        return $component;
    }

    /**
     * create icon component
     *
     * @param array $props
     * @return IconComponentBuilder
     */
    public function createIconComponent($props)
    {
        $margin = NULL;
        $size = NULL;
        $aspectRatio = NULL;
        $this->util->validate($props, [
            'url' => ['required', 'string', 'active_url', 'regex:/^https:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'],
            'margin' => [Rule::in(['none', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl'])],
            'position' => [Rule::in(['relative', 'absolute'])],
            'offsetTop' => 'string',
            'offsetBottom' => 'string',
            'offsetStart' => 'string',
            'offsetEnd' => 'string',
            'size' => [Rule::in(['xxs', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl', '3xl', '4xl', '5xl'])],
            'aspectRatio' => 'string',
        ]);

        if (isset($props['margin'])) {
            $margin = $props['margin'];
        }
        if (isset($props['size'])) {
            $size = $props['size'];
        }
        if (isset($props['aspectRatio'])) {
            $aspectRatio = $props['aspectRatio'];
        }

        $component = new Flex\ComponentBuilder\IconComponentBuilder($props['url'], $margin, $size, $aspectRatio);

        if (isset($props['position'])) {
            $component->setPosition($props['position']);
        }
        if (isset($props['offsetTop'])) {
            $component->setOffsetTop($props['offsetTop']);
        }
        if (isset($props['offsetBottom'])) {
            $component->setOffsetBottom($props['offsetBottom']);
        }
        if (isset($props['offsetStart'])) {
            $component->setOffsetStart($props['offsetStart']);
        }
        if (isset($props['offsetEnd'])) {
            $component->setOffsetEnd($props['offsetEnd']);
        }

        return $component;
    }

    /**
     * create span component
     *
     * @param array $props
     * @return SpanComponentBuilder
     */
    public function createSpanComponent($props)
    {
        $text = '';
        $color = NULL;
        $size = NULL;
        $weight = NULL;
        $style = NULL;
        $decoration = NULL;
        $this->util->validate($props, [
            'type' => ['required', Rule::in(['span'])],
            'text' => 'string',
            'color' => 'string',
            'size' => [Rule::in(['xxs', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl', '3xl', '4xl', '5xl'])],
            'weight' => [Rule::in(['regular', 'bold'])],
            'style' => [Rule::in(['normal', 'italic'])],
            'decoration' => [Rule::in(['none', 'underline', 'line-through'])],
        ]);

        if (isset($props['text'])) {
            $text = $props['text'];
        }
        if (isset($props['color'])) {
            $color = $props['color'];
        }
        if (isset($props['size'])) {
            $size = $props['size'];
        }
        if (isset($props['weight'])) {
            $weight = $props['weight'];
        }
        if (isset($props['style'])) {
            $style = $props['style'];
        }
        if (isset($props['decoration'])) {
            $decoration = $props['decoration'];
        }
        return new Flex\ComponentBuilder\SpanComponentBuilder($text, $size, $color, $weight, $style, $decoration);
    }

    /**
     * create separator component
     *
     * @param array $props
     * @return SeparatorComponentBuilder
     */
    public function createSeparatorComponent($props)
    {
        $margin = NULL;
        $color = NULL;
        $this->util->validate($props, [
            'margin' => [Rule::in(['none', 'xs', 'sm', 'md', 'lg', 'xl', 'xxl'])],
            'color' => 'string',
        ]);
        if (isset($props['margin'])) {
            $margin = $props['margin'];
        }
        if (isset($props['color'])) {
            $color = $props['color'];
        }
        return new Flex\ComponentBuilder\SeparatorComponentBuilder($margin, $color);
    }

    /**
     * create filler component
     *
     * @param array $props
     * @return FillerComponentBuilder
     */
    public function createFillerComponent($props)
    {
        $flex = NULL;
        $this->util->validate($props, [
            'flex' => 'integer',
        ]);
        if (isset($props['flex'])) {
            $flex = $props['flex'];
        }
        return new Flex\ComponentBuilder\FillerComponentBuilder($flex);
    }

    /**
     * create filler component
     *
     * @param array $props
     * @return FillerComponentBuilder
     */
    public function createSpacerComponent($props)
    {
        $size = NULL;
        $this->util->validate($props, [
            'size' => [Rule::in(['xs', 'sm', 'md', 'lg', 'xl', 'xxl'])],
        ]);
        if (isset($props['size'])) {
            $size = $props['size'];
        }
        return new Flex\ComponentBuilder\SpacerComponentBuilder($size);
    }
}
