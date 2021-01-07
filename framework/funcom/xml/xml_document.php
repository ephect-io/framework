<?php

/*
    Possible regex to replace the strpos based method stuff
    $re = '/(<phx:element.[^>]+?[^\/]>)(.*?)(<\/phx:element>)|(<phx:.+?>)|(<\/phx:\w+>)/is';
    preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

*/

namespace FunCom\Xml;

use FunCom\ElementInterface;
use FunCom\ElementTrait;
use FunCom\Registry\ViewRegistry;

/**
 * Description of axmldocument
 *
 * @author david
 */
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
define('TAG_PATTERN_ANY', "phx:");

class XmlDocument implements ElementInterface
{
    use ElementTrait;

    private $_count = 0;
    private $_cursor = 0;
    private $_matches = [];
    private $_text = STR_EMPTY;
    private $_currentMatchKey = -1;
    private $_match = null;
    private $_list = [];
    private $_depths = [];
    private $_matchesByDepth = [];
    private $_matchesById = [];
    private $_matchesByKey = [];
    private $_offsetsById = [];
    private $_replacingsById = [];
    private $_endOfFile = OPEN_TAG . TAG_PATTERN_ANY . 'eof' . STR_SPACE . TERMINATOR . CLOSE_TAG;

    public function __construct($text)
    {
        $this->_text = $text . $this->_endOfFile;
    }

    public function getMatches(): array
    {
        return $this->_matches;
    }

    public function getMatchById(int $id): ?XmlMatch
    {
        $match = null;

        if (!isset($this->_list[$id])) {
            return $match;
        }

        $match = new XmlMatch($this->_list[$id]);

        return $match;
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

    public function getDepthsOfMatches(): array
    {
        return $this->_matchesByDepth;
    }

    public function getIDsOfMatches(): array
    {
        return $this->_matchesById;
    }

    public function getKeysOfMatches(): array
    {
        return $this->_matchesByKey;
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

    public function resetMatchId(): void
    {
        $this->_currentMatchKey = -1;
    }

    public function getCurrentMatch(): ?XmlMatch
    {
        $currentId = $this->_matchesById[$this->_currentMatchKey];
        if ($this->_match === null || $this->_match->getId() !== $currentId) {
            $this->_match = new XmlMatch($this->_list[$currentId]);
        }

        return $this->_match;
    }

    public function getNextMatch(): ?XmlMatch
    {
        $this->_currentMatchKey++;

        $this->_match = null;
        if ($this->_currentMatchKey == $this->_count) {
            return null;
        }

        return $this->getCurrentMatch();
    }

    public function replaceMatches(XmlDocument $doc, string $text): string
    {
        $masterMatchesById = $this->getIDsOfMatches();
        $masterCount = $this->getCount();
        $masterText = $this->_text;

        $slaveMatchesById = $doc->getIDsOfMatches();
        $slaveCount = $doc->getCount();
        $slaveText = $text;

        for ($i = $masterCount - 1; $i > -1; $i--) {
            $masterId = $masterMatchesById[$i];
            $masterMatch = $this->getMatchById($masterId);
            $replaced = '';

            if ($masterMatch->getMethod() !== 'block') {
                continue;
            }

            if ($masterMatch->hasCloser()) {
                $start = $masterMatch->getStart();
                $closer = $masterMatch->getCloser();
                $length = $closer['endsAt'] - $masterMatch->getStart() + 1;

                $replaced = substr($masterText, $start, $length);
            } else {
                $replaced = $masterMatch->getText();
            }

            for ($j = $slaveCount - 1; $j > -1; $j--) {
                $slaveId = $slaveMatchesById[$j];
                $slaveMatch = $doc->getMatchById($slaveId);
                $replacing = '';

                if (
                    $slaveMatch->getMethod() !== 'block'
                    || $masterMatch->properties('name') !== $slaveMatch->properties('name')
                ) {
                    continue;
                }

                if ($slaveMatch->hasCloser()) {
                    $start = $slaveMatch->getStart();
                    $closer = $slaveMatch->getCloser();
                    $length = $closer['endsAt'] - $slaveMatch->getStart() + 1;

                    $replacing = substr($slaveText, $start, $length);
                } else {
                    $replacing = $slaveMatch->getText();
                }

                $masterText = str_replace($replaced, $replacing, $masterText);

                break;
            }
        }

        $masterText = str_replace($this->_endOfFile, '', $masterText);

        return $masterText;
    }

    public function replaceThisMatch(XmlMatch $match, string $text, string $replacing): string
    {
        if ($match->hasCloser()) {
            $start = $match->getStart();
            $closer = $match->getCloser();
            $length = $closer['endsAt'] - $match->getStart() + 1;

            $offset = strlen($replacing) - $length;
            $this->_offsetsById[$match->getId()] = $offset;
            $offset = 0;

            $currentMatchKey = $this->_matchesByKey[$match->getId()];
            $previousMatchId = isset($this->_matchesById[$currentMatchKey + 1]) ? $this->_matchesById[$currentMatchKey + 1] : $match->getId();

            if (
                !$match->isSibling()
                && !$match->isRegistered()
                && $previousMatchId != $match->getId()
            ) {
                $replacingLength = $closer['startsAt'] - $match->getEnd() - 1;

                if (
                    $match->getDepth() !== $this->_list[$previousMatchId]['depth']
                    && $this->_list[$previousMatchId]['depth'] > 0
                ) {

                    $offset = $this->findOffset($match->getId());

                    if ($offset !== 0) {

                        $replacingLength += $offset;
                        $length += $offset;
                    }

                    $patchStart = $match->getEnd() + 1;
                    $patchEnd = $closer['startsAt'] - 1;
                    $patchLength = $patchEnd - $patchStart + 1;

                    $patchReplacing = substr($text, $patchStart, $patchLength + $offset);

                    $replacing = $patchReplacing;

                }

            }

            $replaced = substr($text, $start, $length);

            $text = str_replace($replaced, $replacing, $text);
        } else {
            $offset = strlen($replacing) - strlen($match->getText());
            $this->_offsetsById[$match->getId()] = $offset;

            $text = str_replace($match->getText(), $replacing, $text);
        }

        return $text;
    }

    private function findOffset(int $parentId): int
    {
        $offset = 0;
        $l = count($this->_matchesById);
        for ($j = $l - 1; $j > -1; $j--) {
            $id = $this->_matchesById[$j];
            if ($this->_list[$id]['parentId'] == $parentId) {
                $offset += isset($this->_offsetsById[$id]) ? $this->_offsetsById[$id] : 0;
                $offset += $this->findOffset($id);
            }
        }
        return $offset;
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
        $s = STR_EMPTY;
        $firstName = STR_EMPTY;
        $secondName = STR_EMPTY;
        $cursor = 0;
        $text = $this->_text;
        $parentId = [];
        $depth = 0;
        $parentId[$depth] = -1;

        list($openElementPos, $closeElementPos, $properties) = $this->_parse($tag, $text, $cursor);

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
                $parentId[$depth] = $i - 1;
            }
            $this->_list[$i]['parentId'] = $parentId[$depth];
            $this->_list[$i]['isSibling'] = $isSibling;
            $this->_list[$i]['isRegistered'] = ViewRegistry::read($this->_list[$i]['name']) !== null;

            $this->_list[$i]['properties'] = $properties;

            $cursor = $closeElementPos + 1;
            $secondName = $this->elementName($text, $cursor);

            if (TERMINATOR . $firstName != $secondName) {
                if ($s[1] == TERMINATOR) {
                    $this->_list[$i]['isSibling'] = $isSibling;

                    $pId = !$isSibling && isset($parentId[$depth]) ? $parentId[$depth] : $siblingId;
                    $depth--;
                    $fatherId = $parentId[$depth];

                    $this->_list[$i]['parentId'] = $fatherId;
                    $this->_list[$i]['depth'] = $depth;

                    if (
                        empty($this->_list[$pId]['properties']['content'])
                        && !$this->_list[$i]['isRegistered']
                    ) {
                        $contents = substr($text, $this->_list[$pId]['endsAt'] + 1, $this->_list[$i]['startsAt'] - $this->_list[$pId]['endsAt'] - 1);
                        $this->_list[$pId]['properties']['content'] = '!#base64#' . base64_encode($contents); // uniqid();
                    }

                    $this->_list[$i]['depth'] = $this->_list[$i]['depth'];

                    if ($this->_list[$pId]['isSibling']) {
                        $this->_list[$i]['depth'] = $this->_list[$pId]['depth'];
                    }

                    $this->_list[$pId]['closer'] = $this->_list[$i];
                    $this->_list[$pId]['closer']['parentId'] = $this->_list[$pId]['id'];
                    unset($this->_list[$i]);
                } elseif ($s[1] == QUEST_MARK) {
                } elseif ($s[strlen($s) - 2] == TERMINATOR) {
                } elseif ($s[1] == SKIP_MARK) {
                } else {
                    $sa = explode(':', $secondName);
                    if (isset($sa[1])) {
                        $this->_list[$i]['childName'] = $sa[1];
                    }

                    if ($hasCloser) {
                        $depth++;
                    }
                    $this->_depths[$depth] = 1;
                    if (isset($parentId[$depth])) {
                        unset($parentId[$depth]);
                    }
                }
            }
            list($openElementPos, $closeElementPos, $properties) = $this->_parse($tag, $text, $cursor);

            $cursor = $openElementPos;

            $i++;
        }

        $this->_matchesByDepth = $this->sortMatchesByDepth();
        $this->_matchesById = $this->sortMatchesById();
        $this->_matchesByKey = $this->sortMatchesByKey();

        $this->_count = count($this->_list);

        return ($this->_count > 0);
    }

    public function sortMatchesByDepth(): array
    {
        $maxDepth = count($this->_depths);
        $result = [];
        for ($i = $maxDepth; $i > -1; $i--) {
            foreach ($this->_list as $match) {
                if ($match["depth"] == $i) {
                    array_push($result, $match['id']);
                }
            }
        }

        return $result;
    }

    public function sortMatchesByKey(): array
    {
        $result = [];
        $i = 0;
        foreach ($this->_list as $match) {
            $result[$match['id']] = $i;
            $i++;
        }

        return $result;
    }

    public function sortMatchesById(): array
    {
        $result = [];
        foreach ($this->_list as $match) {
            array_push($result, $match['id']);
        }

        return $result;
    }
}
