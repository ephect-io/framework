<?php

namespace Ephect\Framework\Components\Generators;

use Ephect\Framework\Components\ComponentDeclarationStructure;
use Ephect\Framework\Components\ComponentInterface;
use Ephect\Framework\Crypto\Crypto;
use Ephect\Framework\Registry\ComponentRegistry;

define('TERMINATOR', '/');
define('SKIP_MARK', '!');
define('QUEST_MARK', '?');
define('QUOTE', '"');
define('OPEN_TAG', '<');
define('CLOSE_TAG', '>');
define('TAB_MARK', "\t");
define('LF_MARK', "\n");
define('CR_MARK', "\r");
define('STR_EMPTY', '');
define('STR_SPACE', ' ');

class ComponentParser extends Parser
{
    protected $depths = [];
    protected $idListByDepth = [];
    protected $list = [];

    public function __construct(ComponentInterface $comp)
    {
        parent::__construct($comp);

        ComponentRegistry::uncache();
    }

    public function doDeclaration(): ComponentDeclarationStructure
    {
        $this->doComponents();
        $func = $this->doFunctionDeclaration();
        $decl = ['type' => $func[0], 'name' => $func[1], 'arguments' => $func[2], 'composition' => $this->list];

        $struct = new ComponentDeclarationStructure($decl);

        return $struct;
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
            array_push($result, $match[1]);
        }

        return $result;
    }


    public function isClosedTag(array $tag): bool
    {
        $result = false;

        $text = $tag['text'];
        $result = substr($text, -2) === TERMINATOR . CLOSE_TAG;

        return $result;
    }

    public function isCloseTag(array $tag): bool
    {
        $result = false;

        $text = $tag['text'];
        $result = substr($text, 0, 2) === OPEN_TAG . TERMINATOR;

        return $result;
    }

    public function makeTag($tag, $parentIds, $depth, $isCloser = false): array
    {
        $text = $tag['text'];
        $name =  $tag['name'];

        $i = count($this->list);
        $item = [];

        $item['id'] = $tag['id'];
        $item['name'] =  empty($name) ? 'Fragment' : $name;
        $item['text'] = $text;
        $item['startsAt'] = $tag['startsAt'];
        $item['endsAt'] = $tag['endsAt'];
        if(!$isCloser) {
            $item['uid'] = Crypto::createUID();
            $item['class'] = ComponentRegistry::read($item['name']);
            $item['method'] = 'echo';
            $item['component'] = $this->component->getFullyQualifiedFunction();
            $item['props'] = ($item['name'] === 'Fragment') ? [] : $this->doArguments($text);
            $item['depth'] = $depth;
            $item['hasCloser'] = !$isCloser && substr($text, -2) !== TERMINATOR . CLOSE_TAG;
            $item['node'] = false;
        }
        if (!isset($parentIds[$depth])) {
            $parentIds[$depth] = $i - 1;
        }
        $item['parentId'] = $parentIds[$depth];

        return $item;
    }

    public function doComponents(): void
    {

        $list = [];
        $i = 0;
        $text = $this->html;
        $parentIds = [];
        $depth = 0;
        $parentIds[$depth] = -1;
        $allTags = [];
        /**
         * parse only components as defined by JSX
         * $re = '/<\/?([A-Z]\w+)(.*?)>|<\/?>/m';
         */
        // parse all tags comprising HTML ones 

        $re = '/<\/?([A-Z]\w+)(\s|.*?)+?>|<\/?>/m';

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

            array_push($allTags, $tag);
            $i++;

        }

        $this->depths[$depth] = 1;

        $this->allTags = $allTags;
        $l = count($allTags);
        $i = 0;
        while (count($allTags)) {


            // $this->log($allTags, $i);

            if ($i === $l) {
                $i = 0;
                $allTags = array_values($allTags);
                $l = count($allTags);
            }

            $tag = $allTags[$i];

            // $tag = array_shift($allTags);

            if ($this->isClosedTag($tag)) {
                $item  = $this->makeTag($tag, $parentIds, $depth);
                $list[$item['id']] = $item;
                unset($allTags[$i]);

                $i++;

                continue;
            }

            if ($this->isCloseTag($tag) ) {
                $depth--;
            }

            if ($i + 1 < $l) {
                $nextMatch = $allTags[$i + 1];

                if (!$this->isCloseTag($tag) && $this->isCloseTag($nextMatch)) {
                    $item = $this->makeTag($tag, $parentIds, $depth);
                    $closer = $this->makeTag($nextMatch, $parentIds, $depth, true);

                    $closer['contents'] = [];
                    $closer['parentId'] = $item['id'];
                    $closer['contents']['startsAt'] = $item['endsAt'] + 1; // uniqid();
                    $closer['contents']['endsAt'] = $closer['startsAt'] - 1; // uniqid();
                    $contents = substr($this->html,  $closer['contents']['startsAt'],  $closer['contents']['endsAt'] - $closer['contents']['startsAt'] + 1);
                    $closer['contents']['text'] = '!#base64#' . htmlentities(html_entity_decode($contents));

                    $item['closer'] = $closer;

                    $list[$item['id']] = $item;

                    unset($allTags[$i]);
                    unset($allTags[$i + 1]);

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

        $maxDepth = count($this->depths);
        for ($i = $maxDepth; $i > -1; $i--) {
            foreach ($list as $match) {
                if ($match["depth"] == $i) {
                    array_push($this->idListByDepth, $match['id']);
                }
            }
        }

        $this->list = $list;
    }
}
