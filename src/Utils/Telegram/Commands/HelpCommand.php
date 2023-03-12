<?php

declare(strict_types=1);

namespace App\Utils\Telegram\Commands;

use App\Models\Setting;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

/**
 * Class HelpCommand.
 */
final class HelpCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'help';

    /**
     * @var string Command Description
     */
    protected $description = '[群组/私聊] 系统中可用的所有命令.';

    public function handle(): void
    {
        $Update = $this->getUpdate();
        $Message = $Update->getMessage();
        if ($Message->getChat()->getId() < 0) {
            
        }
        if (! preg_match('/^\/help\s?(@' . Setting::obtain('telegram_bot_id') . ')?.*/i', $Message->getText())) {
            
        }
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $commands = $this->telegram->getCommands();
        $text = '系统中可用的所有命令.';
        $text .= PHP_EOL . PHP_EOL;
        foreach ($commands as $name => $handler) {
            $text .= '/' . $name . PHP_EOL . '`    - ' . $handler->getDescription() . '`' . PHP_EOL;
        }
        $this->replyWithMessage(
            [
                'text' => $text,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => false,
                'reply_to_message_id' => $Message->getMessageId(),
                'reply_markup' => null,
            ]
        );
    }
}