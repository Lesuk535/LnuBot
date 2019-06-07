<?php


namespace App\Services\Telegram\Objects;

/**
 * Class Message
 *
 *
 * @method int             gwtMessageId()                   Unique message identifier inside this chat
 * @method User            getFrom()                        Optional. Sender, empty for messages sent to channels
 * @method int             getDate()                        Date the message was sent in Unix time
 * @method Chat            getChat()                        Conversation the message belongs to
 * @method User            getForwardFrom                   Optional. For forwarded messages, sender of the original message
 * @method Chat            getForwardFromChat               Optional. For messages forwarded from channels, information about the original channel
 * @method int             getForwardFromMessageId()        Optional. For messages forwarded from channels, identifier of the original message in the channel
 * @method string          getForwardSignature()            Optional. For messages forwarded from channels, signature of the post author if present
 * @method string          getForwardSenderName()           Optional. Sender's name for messages forwarded from users who disallow adding a link to their account in forwarded messages
 * @method int             getForwardDate()                 Optional. For forwarded messages, date the original message was sent in Unix time
 * @method Message         getReplyToMessage()              Optional. For replies, the original message. Note that the Message object in this field will not contain further reply_to_message fields even if it itself is a reply.
 * @method int             getEditDate()                    Optional. Date the message was last edited in Unix time
 * @method string          getMediaGroupId()                Optional. The unique identifier of a media message group this message belongs to
 * @method string          getText()                        Optional. For text messages, the actual UTF-8 text of the message, 0-4096 characters.
 * @method Audio           getAudio()                       Optional. Message is an audio file, information about the file
 * @method Document        getDocument()                    Optional. Message is a general file, information about the file
 * @method PhotoSize[]     getPhoto()                       Optional. Message is a photo, available sizes of the photo
 * @method Sticker         getSticker()                     Optional. Message is a sticker, information about the sticker
 * @method Video           getVideo()                       Optional. Message is a video, information about the video
 * @method Voice           getVoice                         Optional. Message is a voice message, information about the file
 * @method string          getCaption                       Optional. Caption for the animation, audio, document, photo, video or voice, 0-1024 characters
 * @method Contact         getContact                       Optional. Message is a shared contact, information about the contact
 * @method Location        getLocation                      Optional. Message is a shared location, information about the location
 * @method string          getNewChatTitle                  Optional. A chat title was changed to this value
 */
class Message extends BaseObject
{

    public function relations()
    {
        return [
            'chat'              => Chat::class,
            'from'              => User::class,
            'forward_rom'       => User::class,
            'forward_from_chat' => Chat::class,
            'reply_to_message'  => self::class,
            'audio'             => Audio::class,
            'document'          => Document::class,
            'photo'             => PhotoSize::class,
            'sticker'           => Sticker::class,
            'video'             => Video::class,
            'voice'             => Voice::class,
            'contact'           => Contact::class,
            'location'          => Location::class,
        ];
    }

    public function getObjectName()
    {
        return 'message';
    }

}