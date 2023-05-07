<?php

namespace Ephect\Framework\Components\Generators;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Components\ComponentDeclarationStructure;
use Ephect\Framework\Components\ComponentInterface;
use Ephect\Framework\Crypto\Crypto;
use Ephect\Framework\Registry\ComponentRegistry;

define('TERMINATOR', '/');
define('SKIP_MARK', '!');
define('QUEST_MARK', '?');
define('QUOTE', '"');
define('OPEN_TAG', '&lt;');
define('CLOSE_TAG', '&gt;');
define('TAB_MARK', "\t");
define('LF_MARK', "\n");
define('CR_MARK', "\r");
define('STR_EMPTY', '');
define('STR_SPACE', ' ');

class Decomposer extends Parser implements ParserInterface
{
    protected array $depths = [];
    protected array $idListByDepth = [];
    protected array $list = [];

    public function __construct(string|ComponentInterface $comp)
    {
        parent::__construct($comp);

        ComponentRegistry::uncache();
    }

    public function doDeclaration(string $uid): ComponentDeclarationStructure
    {
        $this->doComponents();
        $func = $this->doFunctionDeclaration();
        $decl = ['uid' => $uid, 'type' => $func[0], 'name' => $func[1], 'arguments' => $func[2], 'composition' => $this->list];

        return new ComponentDeclarationStructure($decl);
    }

    public function getList(): array
    {
        return $this->list;
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

    /** TO BE DONE on bas of regex101 https://regex101.com/r/QZejMW/2/ */
    public function doFunctionDeclaration(): ?array
    {
        $result = [];
        $re = '/(function)[ ]+([\w]+)[ ]*\(((\s|.*?)*)\)/m';

        $str = $this->html;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {

            $args = $this->doFunctionArguments($match[3]);
            $result = [$match[1], $match[2], $args];
        }

        return $result;
    }

    private function doFunctionArguments(string $arguments): ?array
    {
        $result = [];
        $re = '/([\,]?[\.]?\$[\w]+)/s';

        $str = $arguments;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $result[] = $match[1];
        }

        return $result;
    }


    protected function isSingleTag(array $tag): bool
    {
        $text = $tag['text'];
        if (empty($text) || $tag['name'] === 'Fragment') {
            return false;
        }

        return substr($text, -5) === TERMINATOR . CLOSE_TAG;
    }

    protected function isCloseTag(array $tag): bool
    {
        $text = $tag['text'];
        if (empty($text)) {
            return false;
        }
        return substr($text, 0, 5) === OPEN_TAG . TERMINATOR;
    }

    protected function makeTag($tag, $parentIds, $depth, $hasCloser, $isCloser = false): array
    {
        $text = $tag['text'];
        $name =  $tag['name'];

        $i = count($this->list);
        $item = [];

        $fqName = '';
        if (is_object($this->component)) {
            $fqName = $this->component->getFullyQualifiedFunction();
        }

        $item['id'] = $tag['id'];
        $item['name'] =  empty($name) ? 'Fragment' : $name;
        $item['text'] = $text;
        $item['startsAt'] = $tag['startsAt'];
        $item['endsAt'] = $tag['endsAt'];
        if (!$isCloser) {
            $item['uid'] = Crypto::createUID();
            $item['class'] = ComponentRegistry::read($item['name']);
            $item['method'] = 'echo';
            $item['component'] = $fqName;
            $item['props'] = ($item['name'] === 'Fragment') ? [] : $this->doArguments($text);
            $item['depth'] = $depth;
            $item['hasCloser'] = $hasCloser;
            $item['node'] = false;
            $item['isSingle'] = false;
        }
        if (!isset($parentIds[$depth])) {
            $parentIds[$depth] = $i - 1;
        }
        $item['parentId'] = $parentIds[$depth];

        return $item;
    }

    protected function collectTags(string $text, string $tag = "\w+"): array
    {
        $result = [];

        $re = <<< REGEX
        /&lt;\/?($tag)((\s|.*?)*)\/?&gt;/
        REGEX;

        preg_match_all($re, $text, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER, 0);

        $i = 0;

        foreach ($matches as $match) {
            $tag = $match;
            $tag['id'] = $i;
            $tag['text'] = $match[0][0];
            $tag['name'] = !isset($match[1]) ? 'Fragment' : $match[1][0];
            $tag['startsAt'] = $match[0][1];
            $tag['endsAt'] = $match[0][1] + strlen($tag['text']) - 1;

            unset($tag[0]);
            unset($tag[1]);
            unset($tag[2]);
            unset($tag[3]);

            $result[] = $tag;
            $i++;
        }

        return $result;
    }

    protected function splitTags(array $allTags): array
    {
        $l = count($allTags);
        $i = 0;
        $isFinished = false;
        $spinner = 0;
        $spinnerMax = $l;
        $isSpinning = false;
        $singleTags = [];
        $workTags = [];


        while (count($allTags) && !$isFinished && !$isSpinning) {

            if ($i === $l) {
                $i = 0;
                $allTags = array_values($allTags);
                $l = count($allTags);
                if ($l === 0) {
                    $isFinished = true;
                    continue;
                }

                $spinner++;
                $isSpinning = $spinner > $spinnerMax + 1;
            }

            $tag = $allTags[$i];
            if (count($allTags) === 1 && $tag['name'] === 'Eof') {
                $isFinished = true;
                continue;
            }

            if ($this->isSingleTag($tag) && $tag['name'] !== 'Eof') {
                $workTags[$i] = $allTags[$i];
                unset($allTags[$i]);
                $i++;

                continue;
            }


            if ($i + 1 < $l) {
                $nextMatch = $allTags[$i + 1];

                if (!$this->isCloseTag($tag) && $this->isCloseTag($nextMatch)) {

                    if ($tag['name'] !== $nextMatch['name']) {
                        unset($allTags[$i]);
                        $singleTags[] = $tag;
                        $singleIdList[] = $i;
                        $i++;
                        continue;
                    }

                    $workTags[$i] = $allTags[$i];
                    $workTags[$i + 1] = $allTags[$i + 1];
                    unset($allTags[$i]);
                    unset($allTags[$i + 1]);

                    $i += 2;

                    continue;
                }
            }
            $i++;
        }

        return [$workTags, $singleTags];
    }

    protected function replaceTags(string $text, array $tags): string
    {
        $result = $text;
        $c = count($tags);

        for ($i = $c - 1; $i > -1; $i--) {
            if (!isset($tags[$i])) {
                continue;
            }
            $tag = $tags[$i];
            $tag['text'] = substr($tag['text'], 0, -4) . TERMINATOR . CLOSE_TAG;

            $begin = substr($result, 0, $tag['startsAt']);
            $end = substr($result,  $tag['endsAt'] + 1);

            $result = $begin . $tag['text'] . $end;
        }

        return $result;
    }

    public function doComponents(string $tag = "\w+"): void
    {

        $list = [];
        $i = 0;
        $text = $this->html;
        $text .= "\n<Eof />";
        $parentIds = [];
        $depth = 0;
        $parentIds[$depth] = -1;
        $allTags = [];
        $singleTags = [];
        $singleIdList = [];
        $workTags = [];

        $allTags = $this->collectTags($text);

        [$workTags, $singleTags] = $this->splitTags($allTags);

        if (count($singleTags)) {
            foreach ($singleTags as $tag) {
                $singleIdList[] = $tag['id'];
            }
            $text = $this->replaceTags($text, $singleTags);
            $workTags = $this->collectTags($text);
        }


        Console::log($workTags);

        $l = count($workTags);
        $i = 0;
        $isFinished = false;
        $spinner = 0;
        $spinnerMax = $l;
        $isSpinning = false;

        $this->depths[$depth] = 1;

        while (count($workTags) && !$isFinished && !$isSpinning) {

            if ($i === $l) {
                $i = 0;
                $workTags = array_values($workTags);
                $l = count($workTags);
                if ($l === 0) {
                    $isFinished = true;
                    continue;
                }

                $spinner++;
                $isSpinning = $spinner > $spinnerMax + 1;
            }

            $tag = $workTags[$i];
            if (count($workTags) === 1 && $tag['name'] === 'Eof') {
                $isFinished = true;
                continue;
            }

            if ($this->isSingleTag($tag) && $tag['name'] !== 'Eof') {
                $item  = $this->makeTag($tag, $parentIds, $depth, false);
                $item['isSingle'] = in_array($tag['id'], $singleIdList);

                $list[$item['id']] = $item;
                unset($workTags[$i]);

                $i++;

                continue;
            }

            if ($this->isCloseTag($tag)) {
                $depth--;
            }

            if ($i + 1 < $l) {
                $nextMatch = $workTags[$i + 1];

                if (!$this->isCloseTag($tag) && $this->isCloseTag($nextMatch)) {
                    $item = $this->makeTag($tag, $parentIds, $depth, true);
                    $closer = $this->makeTag($nextMatch, $parentIds, $depth, false, true);

                    $closer['contents'] = [];
                    $closer['parentId'] = $item['id'];
                    $closer['contents']['startsAt'] = $item['endsAt'] + 1; // uniqid();
                    $closer['contents']['endsAt'] = $closer['startsAt'] - 1; // uniqid();
                    $contents = substr($this->html, $closer['contents']['startsAt'], $closer['contents']['endsAt'] - $closer['contents']['startsAt'] + 1);
                    $closer['contents']['text'] = '!#base64#' . base64_encode($contents);

                    $item['closer'] = $closer;

                    $list[$item['id']] = $item;

                    unset($workTags[$i]);
                    unset($workTags[$i + 1]);

                    $i += 2;

                    continue;
                }

                if (!$this->isCloseTag($tag) && !$this->isCloseTag($nextMatch)) {
                    $depth++;
                    $parentIds[$depth] = $tag['id'];
                }
            }

            $this->depths[$depth] = 1;

            $i++;
        }

        ksort($list);
        $l = count($list);

        for ($i = 0; $i < $l; $i++) {
        }

        $maxDepth = count($this->depths);
        for ($i = $maxDepth; $i > -1; $i--) {
            foreach ($list as $match) {
                if ($match["depth"] == $i) {
                    $this->idListByDepth[] = $match['id'];
                }
            }
        }

        $this->list = $list;
    }
}
