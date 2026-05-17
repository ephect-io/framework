<?php

namespace Ephect\Framework\Xml;

use Ephect\Framework\Element;

define('QUOTE', '"');
define('OPEN_TAG', '<');
define('CLOSE_TAG', '>');
define('TERMINATOR', '/');
define('TAB_MARK', "\t");
define('LF_MARK', "\n");
define('CR_MARK', "\r");
define('SKIP_MARK', '!');
define('QUEST_MARK', '?');
define('STR_EMPTY', '');
define('STR_SPACE', ' ');
define('TAG_PATTERN_ANY', "[A-Z\w]+");

class XmlDocument extends Element
{
    private int $_count = 0;
    private int $_cursor = 0;
    private array $_matches = [];
    private string $_text = STR_EMPTY;
    private int $_id = -1;
    private ?XmlMatch $_match = null;
    private array $_list = [];
    private array $_depths = [];
    private array $_matchesByDepth = [];
    private int $_endPos = -1;
    private array $_tags = [];

    public function __construct(string $text)
    {
        $this->_text = $text . OPEN_TAG . TAG_PATTERN_ANY . 'eof' . STR_SPACE . TERMINATOR . CLOSE_TAG;
        $this->_endPos = strlen($this->_text);
        $this->_tags = $this->_grabTags();
    }

    private function _grabTags(string $tag = TAG_PATTERN_ANY): array
    {
        $result = [];
        preg_match_all('/<' . $tag . '[^>]*>/', $this->_text, $result);

        return $result[0];
    }

    public function getTags(): array
    {
        return $this->_tags;
    }

    public function getMatches(): array
    {
        return $this->_matches;
    }

    public function getCount(): int
    {
        return $this->_count;
    }

    public function getCursor(): int
    {
        return $this->_cursor;
    }

    public function getList(): array
    {
        return $this->_list;
    }

    public function fieldValue(int $i, string $field, string $value)
    {
        $this->_list[$i][$field] = $value;
    }

    public function getMaxDepth(): int
    {
        return count($this->_depths);
    }

    public function getMatchesByDepth(): array
    {
        return $this->_matchesByDepth;
    }

    public function elementName(string $s, int $offset, string $tag = TAG_PATTERN_ANY): string
    {
        if (!isset($offset)) {
            $offset = 0;
        }
        $result = STR_EMPTY;
        $s2 = STR_EMPTY;

        $openElementPos = 0;
        $closeElementPos = 0;
        $spacePos = 0;

        if ($offset > 0 && $offset < strlen($s)) {
            //$openElementPos = $offset;
            $openElementPos = strpos($s, OPEN_TAG . $tag, $offset);
        } else {
            $openElementPos = strpos($s, OPEN_TAG . $tag);
        }

        if ($openElementPos == -1) {
            return $result;
        }

        $s2 = substr($s, $openElementPos, strlen($s) - $openElementPos);
        $spacePos = strpos($s2, STR_SPACE);
        $closeElementPos = strpos($s2, CLOSE_TAG);
        if ($closeElementPos > -1 && $spacePos > -1) {
            if ($closeElementPos < $spacePos) {
                $result = substr($s, $openElementPos + 1, $closeElementPos - 1);
            } else {
                $result = substr($s, $openElementPos + 1, $spacePos - 1);
            }
        } elseif ($closeElementPos > -1) {
            $result = substr($s, $openElementPos + 1, $closeElementPos - 1);
        }

        return $result;
    }

    public function getMatch(): ?XmlMatch
    {
        if ($this->_match == null) {
            //$this->_match = new XmlMatch($this->_list[$this->_matchesByDepth[$this->_id]]);
            $this->_match = new XmlMatch($this->_list[$this->_id]);
        }

        return $this->_match;
    }

    public function nextMatch(): ?XmlMatch
    {
        $this->_match = null;
        if ($this->_id == $this->_count - 1) {
            return null;
        }

        $this->_id++;

        return $this->getMatch();
    }

    public function replaceMatch(string $replace): string
    {
        if ($this->_match->hasChildren()) {
            $start = $this->_match->getStart();
            $length = $this->_match->getEnd() - $this->_match->getStart() + 1;
            $needle = substr($this->_text, $start, $length);
            $this->_text = str_replace($needle, $replace, $this->_text);
        } else {
            $this->_text = str_replace($this->_match->getText(), $replace, $this->_text);
        }

        return $this->_text;
    }

    public function replaceThisMatch(XmlMatch $match, string $text, string $replace): string
    {

        if ($match->hasChildren()) {
            $start = $match->getStart();
            $closer = $match->getCloser();
            $length = $closer['endsAt'] - $match->getStart() + 1;
            $needle = substr($text, $start, $length);
            $text = str_replace($needle, $replace, $text);
        } else {
            $text = str_replace($match->getText(), $replace, $text);
        }

        return $text;
    }

    private function _parse(string $tag, string $text, string $cursor): array
    {
        $properties = [];

        $endElementPos = strpos($text, OPEN_TAG . TERMINATOR . $tag, $cursor);
        $openElementPos = strpos($text, OPEN_TAG . $tag, $cursor);
        if ($openElementPos > -1 && $endElementPos > -1 && $openElementPos > $endElementPos) {
            $openElementPos = $endElementPos;
            $closeElementPos = strpos($text, CLOSE_TAG, $openElementPos);
            return [$openElementPos, $closeElementPos, $properties];
        }

        $spacePos = strpos($text, STR_SPACE, $openElementPos);
        $equalPos = strpos($text, '=', $spacePos);
        $openQuotePos = strpos($text, QUOTE, $openElementPos);
        $closeQuotePos = strpos($text, QUOTE, $openQuotePos + 1);
        $lastCloseQuotePos = $closeQuotePos;
        $closeElementPos =  strpos($text, CLOSE_TAG, $lastCloseQuotePos);
        while ($openQuotePos > -1 && $closeQuotePos < $closeElementPos) {
            $key = substr($text, $spacePos + 1, $equalPos - $spacePos - 1);
            $value = substr($text, $openQuotePos + 1, $closeQuotePos - $openQuotePos - 1);
            $properties[trim($key)] = $value;
            $lastCloseQuotePos = $closeQuotePos;

            $spacePos = strpos($text, STR_SPACE, $closeQuotePos);
            $equalPos = strpos($text, '=', $spacePos);
            $openQuotePos = strpos($text, QUOTE, $closeQuotePos + 1);
            $closeQuotePos = strpos($text, QUOTE, $openQuotePos + 1);
            $closeElementPos =  strpos($text, CLOSE_TAG, $lastCloseQuotePos);
            if ($openQuotePos < $closeElementPos && $closeQuotePos > $closeElementPos) {
                $closeElementPos =  strpos($text, CLOSE_TAG, $closeQuotePos);
            }
        }
        if ($lastCloseQuotePos > -1) {
            $closeElementPos =  strpos($text, CLOSE_TAG, $lastCloseQuotePos);
        } else {
            $closeElementPos =  strpos($text, CLOSE_TAG, $openElementPos);
        }

        return [$openElementPos, $closeElementPos, $properties];
    }

    public function matchAll(string $tag = TAG_PATTERN_ANY): bool
    {
        $i = 0;
        $j = -1;

        $s = STR_EMPTY;
        $firstName = STR_EMPTY;
        $secondName = STR_EMPTY;

        $cursor = 0;

        $text = $this->_text;

        list($openElementPos, $closeElementPos, $properties) = $this->_parse($tag, $text, $cursor);

        $parentId = [];
        $depth = 0;
        $parentId[$depth] = -1;

        //$this->_depths[$depth] = 1;

        while ($openElementPos > -1 && $closeElementPos > $openElementPos) {
            $siblingId = $i - 1;
            $s = trim(substr($text, $openElementPos, $closeElementPos - $openElementPos + 1));
            $firstName = $this->elementName($s, $cursor);

            $arr = explode(':', $firstName);
            if (!isset($arr[1])) {
                $arr[1] = '';
            }

            if ($arr[1] == 'eof') {
                break;
            }
            $terminator1 = $s[1];
            $terminator2 = $s[strlen($s) - 2];
            $hasCloser = $terminator1 != TERMINATOR && $terminator2 != TERMINATOR;
            $isSibling = isset($this->_list[$siblingId]) && $this->_list[$siblingId]['hasCloser'];

            $this->_list[$i]['id'] = $i;
            $this->_list[$i]['method'] = $arr[1];
            $this->_list[$i]['element'] = $s;
            $this->_list[$i]['name'] = $arr[1];
            $this->_list[$i]['startsAt'] = $openElementPos;
            $this->_list[$i]['endsAt'] = $closeElementPos;
            $this->_list[$i]['depth'] = $depth;
            $this->_list[$i]['hasCloser'] = $hasCloser;
            $this->_list[$i]['childName'] = '';
            if (!isset($parentId[$depth])) {
                // $parentId[$depth] = ($siblingId > 0) ? $siblingId : -1;
                $parentId[$depth] = $i - 1;
            }
            $this->_list[$i]['parentId'] = $parentId[$depth];
            /** begin */
            if ($isSibling && $depth > -1) {
                $parentId[$depth - 1] = $siblingId;
            }

            // $this->_list[$i]['parentId'] = (isset($parentId[$depth - 1])) ?  $parentId[$depth - 1] : -1;
            $this->_list[$i]['isSibling'] = $isSibling;

            if (isset($this->_list[$siblingId]) && $this->_list[$siblingId]['isSibling']) {
                $parentId[$depth - 1] = $i;
            }
            /** end */
            $this->_list[$i]['properties'] = $properties;

            $cursor = $closeElementPos + 1;
            $secondName = $this->elementName($text, $cursor);

            if (TERMINATOR . $firstName != $secondName) {
                if ($s[1] == TERMINATOR) {
                    $this->_list[$i]['isSibling'] = $isSibling;

                    /** begin */
                    // $pId = !$isSibling ? $this->_list[$i]['parentId'] : $siblingId;
                    $pId = !$isSibling && isset($parentId[$depth]) ? $parentId[$depth] : $siblingId;

                    if ($this->_list[$pId]['isSibling']) {
                        $depth--;
                    }
                    if ($this->_list[$i]['isSibling']) {
                        $depth--;
                    }
                    /** end */

                    $this->_list[$i]['depth'] = $depth;

                    if ((empty($this->_list[$pId]['properties']['content']))) {
                        $contents = substr($text, $this->_list[$pId]['endsAt'] + 1, $this->_list[$i]['startsAt'] - $this->_list[$pId]['endsAt'] - 1);
                        $this->_list[$pId]['properties']['content'] = '!#base64#' . base64_encode($contents); // uniqid();
                    }

                    /** begin */

                    $this->_list[$i]['depth'] = $this->_list[$i]['depth'];

                    if ($this->_list[$pId]['isSibling']) {
                        $this->_list[$i]['depth'] = $this->_list[$pId]['depth'];
                    }
                    $this->_list[$i]['parentId'] = $this->_list[$pId]['id'];
                    /** end */


                    $this->_list[$pId]['closer'] = $this->_list[$i];
                    unset($this->_list[$i]);
                } elseif ($s[1] == QUEST_MARK) {
                } elseif ($s[strlen($s) - 2] == TERMINATOR) {
                } elseif ($s[1] == SKIP_MARK) {
                } else {
                    $sa = explode(':', $secondName);
                    if (isset($sa[1])) {
                        $this->_list[$i]['childName'] = $sa[1];
                    }
                    /** begin */

                    if ($hasCloser) {
                        $depth++;
                    }
                    $this->_depths[$depth] = 1;
                    if (isset($parentId[$depth])) {
                        unset($parentId[$depth]);
                    }
                    /** end */
                }
            }
            list($openElementPos, $closeElementPos, $properties) = $this->_parse($tag, $text, $cursor);

            $cursor = $openElementPos;

            $i++;
        }

        $this->_matchesByDepth = $this->sortMatchesByDepth();

        $this->_count = count($this->_list);

        return ($this->_count > 0);
    }

    public function sortMatchesByDepth(): array
    {
        $maxDepth = count($this->_depths);
        $result = [];
        for ($i = $maxDepth; $i > -1; $i--) {
            foreach ($this->_list as $part) {
                if ($part["depth"] == $i) {
                    $count = count($result);
                    $result[$count] = $part['id'];
                }
            }
        }

        return $result;
    }
}
