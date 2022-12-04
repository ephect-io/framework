<?php

namespace Ephect\Framework\Xml;

use Ephect\Framework\Components\ComponentInterface;
use Ephect\Framework\Components\Generators\Parser;
use Ephect\Framework\Registry\ComponentRegistry;

define('QUOTE', '"');
define('OPEN_TAG', '<');
define('CLOSE_TAG', '>');
// define('TERMINATOR', '/');
define('TAB_MARK', "\t");
define('LF_MARK', "\n");
define('CR_MARK', "\r");
// define('SKIP_MARK', '!');
// define('QUEST_MARK', '?');
define('STR_EMPTY', '');
define('STR_SPACE', ' ');
define('TAG_PATTERN_ANY', "phx:");

class XmlDocument extends Parser
{
    private $count = 0;
    private $cursor = 0;
    private $matches = [];
    private $text = STR_EMPTY;
    private $currentMatchKey = -1;
    private $match = null;
    private $list = [];
    private $depths = [];
    private $idListByDepth = [];
    private $matchesById = [];
    private $matchesByKey = [];
    private $offsetsById = [];
    private $replacingsById = [];
    private $endOfFile = OPEN_TAG . TAG_PATTERN_ANY . 'eof' . STR_SPACE . TERMINATOR . CLOSE_TAG;

    // public function __construct($text)
    // {
    //     $this->text = $text . $this->endOfFile;
    // }

    
    public function __construct(ComponentInterface $comp)
    {
        parent::__construct($comp);

        ComponentRegistry::uncache();
    }

    public function getMatches(): array
    {
        return $this->matches;
    }

    public function getMatchById(int $id): ?XmlMatch
    {
        $match = null;

        if (!isset($this->list[$id])) {
            return $match;
        }

        $match = new XmlMatch($this->list[$id]);

        return $match;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getCursor(): int
    {
        return $this->cursor;
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function fieldValue(int $i, string $field, string $value)
    {
        $this->list[$i][$field] = $value;
    }

    public function getMaxDepth(): int
    {
        return count($this->depths);
    }

    public function getDepthsOfMatches(): array
    {
        return $this->idListByDepth;
    }

    public function getIDsOfMatches(): array
    {
        return $this->matchesById;
    }

    public function getKeysOfMatches(): array
    {
        return $this->matchesByKey;
    }

    
    public function getHtml(): string
    {
        return $this->html;
    }

    public function getDepths(): array
    {
        return $this->depths;
    }

    public function getIdListByDepth(): array
    {
        return $this->idListByDepth;
    }

    public function markupComponents(): void
    {
        $re = '/(<\/?)([A-Z]\w+)((\s|.*?)+)(>)|(<\/?)(>)/m';

        $subst = '$1' . TAG_PATTERN_ANY . '$2$3$4$5';

        $this->text = preg_replace($re, $subst, $this->text);

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
        $this->currentMatchKey = -1;
    }

    public function getCurrentMatch(): ?XmlMatch
    {
        $currentId = $this->matchesById[$this->currentMatchKey];
        if ($this->match === null || $this->match->getId() !== $currentId) {
            $this->match = new XmlMatch($this->list[$currentId]);
        }

        return $this->match;
    }

    public function getNextMatch(): ?XmlMatch
    {
        $this->currentMatchKey++;

        $this->match = null;
        if ($this->currentMatchKey == $this->count) {
            return null;
        }

        return $this->getCurrentMatch();
    }

    public function replaceMatches(TXmlDocument $doc, string $text): string
    {
        $masterMatchesById = $this->getIDsOfMatches();
        $masterCount = $this->getCount();
        $masterText = $this->text;

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

        $masterText = str_replace($this->endOfFile, '', $masterText);

        return $masterText;
    }

    public function replaceThisMatch(XmlMatch $match, string $text, string $replacing): string
    {
        if ($match->hasCloser()) {
            $start = $match->getStart();
            $closer = $match->getCloser();
            $length = $closer['endsAt'] - $match->getStart() + 1;

            $offset = strlen($replacing) - $length;
            $this->offsetsById[$match->getId()] = $offset;
            $offset = 0;

            $currentMatchKey = $this->matchesByKey[$match->getId()];
            $previousMatchId = isset($this->matchesById[$currentMatchKey + 1]) ? $this->matchesById[$currentMatchKey + 1] : $match->getId();

            if (
                !$match->isSibling()
                && !$match->isRegistered()
                && $previousMatchId != $match->getId()
            ) {
                $replacingLength = $closer['startsAt'] - $match->getEnd() - 1;

                if (
                    $match->getDepth() !== $this->list[$previousMatchId]['depth']
                    && $this->list[$previousMatchId]['depth'] > 0
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
            $this->offsetsById[$match->getId()] = $offset;

            $text = str_replace($match->getText(), $replacing, $text);
        }

        return $text;
    }

    private function findOffset(int $parentId): int
    {
        $offset = 0;
        $l = count($this->matchesById);
        for ($j = $l - 1; $j > -1; $j--) {
            $id = $this->matchesById[$j];
            if ($this->list[$id]['parentId'] == $parentId) {
                $offset += isset($this->offsetsById[$id]) ? $this->offsetsById[$id] : 0;
                $offset += $this->findOffset($id);
            }
        }
        return $offset;
    }

    private function parse(string $tag, string $text, string $cursor): array
    {
        $properties = [];

        $endElementPos = strpos($text, OPEN_TAG . TERMINATOR . $tag, $cursor);
        $openElementPos = strpos($text, OPEN_TAG . $tag, $cursor);
        if ($openElementPos > -1 && $endElementPos > -1 && $openElementPos > $endElementPos) {
            $openElementPos = $endElementPos;
            $closeElementPos = strpos($text, CLOSE_TAG, $openElementPos);
            return [$openElementPos, $closeElementPos, $properties];
        }

        // $spacePos = strpos($text, STR_SPACE, $openElementPos);
        // $equalPos = strpos($text, '=', $spacePos);
        // $openQuotePos = strpos($text, QUOTE, $openElementPos);
        // $closeQuotePos = strpos($text, QUOTE, $openQuotePos + 1);
        // $lastCloseQuotePos = $closeQuotePos;
        $closeElementPos =  strpos($text, CLOSE_TAG, $openElementPos);
        // while ($openQuotePos > -1 && $closeQuotePos < $closeElementPos) {
        //     // $key = substr($text, $spacePos + 1, $equalPos - $spacePos - 1);
        //     // $value = substr($text, $openQuotePos + 1, $closeQuotePos - $openQuotePos - 1);
        //     // $properties[trim($key)] = $value;
        //     $lastCloseQuotePos = $closeQuotePos;

        //     // $spacePos = strpos($text, STR_SPACE, $closeQuotePos);
        //     // $equalPos = strpos($text, '=', $spacePos);
        //     $openQuotePos = strpos($text, QUOTE, $closeQuotePos + 1);
        //     $closeQuotePos = strpos($text, QUOTE, $openQuotePos + 1);
        //     $closeElementPos =  strpos($text, CLOSE_TAG, $lastCloseQuotePos);
        //     if ($openQuotePos < $closeElementPos && $closeQuotePos > $closeElementPos) {
        //         $closeElementPos =  strpos($text, CLOSE_TAG, $closeQuotePos);
        //     }
        // }
        // if ($lastCloseQuotePos > -1) {
        //     $closeElementPos =  strpos($text, CLOSE_TAG, $lastCloseQuotePos);
        // } else {
        //     $closeElementPos =  strpos($text, CLOSE_TAG, $openElementPos);
        // }

        return [$openElementPos, $closeElementPos, $properties];
    }

    public function matchAll(string $tag = TAG_PATTERN_ANY): bool
    {
        $i = 0;
        $s = STR_EMPTY;
        $firstName = STR_EMPTY;
        $secondName = STR_EMPTY;
        $cursor = 0;
        $text = $this->text;
        $parentId = [];
        $depth = 0;
        $parentId[$depth] = -1;

        $list = [];

        [$openElementPos, $closeElementPos, $properties] = $this->parse($tag, $text, $cursor);

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
            $isSibling = isset($this->list[$siblingId]) && $this->list[$siblingId]['hasCloser'];



            $list[$i]['id'] = $i;
            $list[$i]['name'] =  !isset($arr[1]) ? 'Fragment' : $arr[1];
            $list[$i]['class'] = ComponentRegistry::read($list[$i]['name']);
            $list[$i]['method'] = 'echo';
            $list[$i]['component'] = $this->component->getFullyQualifiedFunction();
            $list[$i]['text'] = $s;
            $list[$i]['startsAt'] = $openElementPos;
            $list[$i]['endsAt'] = $closeElementPos;
            $list[$i]['props'] = ($list[$i]['name'] === 'Fragment') ? [] : $this->doArguments($s);
            $list[$i]['depth'] = $depth;
            $list[$i]['hasCloser'] = $hasCloser;
            $list[$i]['isCloser'] = false;
            $list[$i]['childName'] = '';
            if (!isset($parentId[$depth])) {
                $parentId[$depth] = $i - 1;
            }
            $list[$i]['parentId'] = $parentId[$depth];
            $list[$i]['isSibling'] = $isSibling;
            // $list[$i]['isRegistered'] = TRegistry::classInfo($list[$i]['name']) !== null;

            
            $cursor = $closeElementPos + 1;
            $secondName = $this->elementName($text, $cursor);

            if (TERMINATOR . $firstName != $secondName) {
                if ($s[1] == TERMINATOR) {
                    $list[$i]['isSibling'] = $isSibling;

                    $pId = !$isSibling && isset($parentId[$depth]) ? $parentId[$depth] : $siblingId;
                    $depth--;
                    $fatherId = $parentId[$depth];

                    $list[$i]['parentId'] = $fatherId;
                    $list[$i]['depth'] = $depth;

                    if (
                        empty($list[$pId]['props']['content'])
                        && !$list[$i]['isRegistered']
                    ) {
                        $contents = substr($text, $list[$pId]['endsAt'] + 1, $list[$i]['startsAt'] - $list[$pId]['endsAt'] - 1);
                        $list[$pId]['props']['content'] = '!#base64#' . $contents; // uniqid();
                    }

                    $list[$i]['depth'] = $list[$i]['depth'];

                    if ($list[$pId]['isSibling']) {
                        $list[$i]['depth'] = $list[$pId]['depth'];
                    }

                    $list[$pId]['closer'] = $list[$i];
                    $list[$pId]['closer']['parentId'] = $list[$pId]['id'];
                    unset($list[$i]);
                } elseif ($s[1] == QUEST_MARK) {
                    continue;
                } elseif ($s[strlen($s) - 2] == TERMINATOR) {
                    continue;
                } elseif ($s[1] == SKIP_MARK) {
                    continue;
                } else {
                    $sa = explode(':', $secondName);
                    if (isset($sa[1])) {
                        $list[$i]['childName'] = $sa[1];
                    }

                    if ($hasCloser) {
                        $depth++;
                    }
                    $this->depths[$depth] = 1;
                    if (isset($parentId[$depth])) {
                        unset($parentId[$depth]);
                    }
                }
            }
            list($openElementPos, $closeElementPos, $properties) = $this->parse($tag, $text, $cursor);

            $cursor = $openElementPos;

            $i++;
        }

        $maxDepth = count($this->depths);
        for ($i = $maxDepth; $i > -1; $i--) {
            foreach ($list as $match) {
                if ($match["depth"] == $i) {
                    array_push($this->idListByDepth, $match['id']);
                }
            }
        }
        
        $this->list = $list;
        $this->count = count($list);

        $this->idListByDepth = $this->sortMatchesByDepth();
        $this->matchesById = $this->sortMatchesById();
        $this->matchesByKey = $this->sortMatchesByKey();


        return ($this->count > 0);
    }

    public function sortMatchesByDepth(): array
    {
        $result = [];

        $maxDepth = count($this->depths);
        for ($i = $maxDepth; $i > -1; $i--) {
            foreach ($this->list as $match) {
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
        foreach ($this->list as $match) {
            $result[$match['id']] = $i;
            $i++;
        }

        return $result;
    }

    public function sortMatchesById(): array
    {
        $result = [];
        foreach ($this->list as $match) {
            array_push($result, $match['id']);
        }

        return $result;
    }
}
