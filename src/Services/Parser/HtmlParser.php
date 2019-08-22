<?php


namespace App\Services\Parser;


class HtmlParser
{
    /**
     * @var int
     */
    private $cursor;

    /**
     * @var string
     */
    private $str;

    public function __construct(string $str = '')
    {
        $this->cursor = 0;
        $this->setString($str);
    }

    /**
     * @param string $str
     */
    public function setString(string $str)
    {
        $this->str = $str;
        $this->defaultCursor();
    }

    /**
     * @return mixed
     */
    public function getString()
    {
        return $this->str;
    }

    /**
     * @param $pattern
     * @return bool
     */
    public function moveTo($pattern): bool
    {
        $cursorPos = strpos($this->str, $pattern, $this->cursor);

        if ($cursorPos === false)
            return false;

        $this->cursor = $cursorPos;

        return true;
    }

    /**
     * @param $pattern
     * @return bool|string
     */
    public function readTo($pattern)
    {
        $cursorPos = strpos($this->str, $pattern, $this->cursor);

        if ($cursorPos === false)
            return false;

        $data = substr($this->str, $this->cursor, $cursorPos - $this->cursor);
        $this->cursor = $cursorPos;
        return $data;
    }

    /**
     * @param $pattern
     * @return bool
     */
    public function moveAfter($pattern): bool
    {
        $cursorPos = strpos($this->str, $pattern, $this->cursor);

        if ($cursorPos === false)
            return false;

        $this->cursor = $cursorPos + strlen($pattern);
        return true;
    }

    /**
     * @param array $pattern
     * @return bool
     */
    public function moveAfterSeveralTimes(array $pattern): bool
    {
        $moveAfter = false;

        foreach ($pattern as $value) {
            $moveAfter = $this->moveAfter($value);
        }

        return $moveAfter;
    }

    /**
     * @param $pattern
     * @return bool|string
     */
    public function readFrom($pattern)
    {
        $cursorPos = strpos($this->str, $pattern);

        if ($cursorPos === false || $cursorPos >= $this->cursor)
            return false;

        $data = substr($this->str, $cursorPos, $this->cursor - $cursorPos);

        return $data;
    }

    /**
     * @param $start
     * @param $tag
     * @return bool
     */
    public function subTag($start, $tag)
    {

        $cursorPos = strpos($this->str, $start, $this->cursor);

        if ($cursorPos === false)
            return false;

        $cursorPos += strlen($start);
        $startCutting = $cursorPos - strlen($start);

        $open = '<' . $tag;
        $close = '</' . $tag .'>';

        $run = 1;

        while ($run) {
            $openPos = strpos($this->str, $open, $cursorPos);
            $closePos = strpos($this->str, $close,  $cursorPos);

            if ($openPos === false || $openPos > $closePos) {
                $cursorPos = $closePos + strlen($close);
                $run--;
            }
            else {
                $cursorPos = $openPos + strlen($open);
                $run++;
            }
        }

        return $data = substr($this->str, $startCutting, $cursorPos - $startCutting);
    }

    public function defaultCursor()
    {
        $this->cursor = 0;
    }

}