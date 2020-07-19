<?php

namespace TelegramBot\Api\Types\Inline\QueryResult;

use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Inline\InputMessageContent;

/**
 * Class InlineQueryResultArticle
 * Represents a link to an article or web page.
 *
 * @package TelegramBot\Api\Types\Inline
 */
class Game extends AbstractInlineQueryResult
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['type', 'id', 'game_short_name'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'type' => true,
        'id' => true,
        'reply_markup' => InlineKeyboardMarkup::class,
        'game_short_name' => true
    ];

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $type = 'game';

    /**
     * @var string
     */
    protected $gameShortName;

    /**
     * InlineQueryResultArticle constructor.
     *
     * @param string $id
     * @param string $title
     * @param string|null $description
     * @param string|null $thumbUrl
     * @param int|null $thumbWidth
     * @param int|null $thumbHeight
     * @param InputMessageContent $inputMessageContent
     * @param InlineKeyboardMarkup|null $inlineKeyboardMarkup
     */
    public function __construct(
        $id,
        $gameShortName,
        $inlineKeyboardMarkup = null
    ) {
        parent::__construct($id, null, null, $inlineKeyboardMarkup);

        $this->gameShortName = $gameShortName;
    }

    /**
     * @return string
     */
    public function getGameShortName()
    {
        return $this->gameShortName;
    }

    /**
     * @param string $gameShortName
     */
    public function setGameShortName($gameShortName)
    {
        $this->gameShortName = $gameShortName;
    }
}
