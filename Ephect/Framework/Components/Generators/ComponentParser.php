<?php

namespace Ephect\Framework\Components\Generators;

use Ephect\Framework\Components\ComponentDeclarationStructure;
use Ephect\Framework\Components\ComponentInterface;
use Ephect\Framework\Crypto\Crypto;
use Ephect\Framework\Registry\ComponentRegistry;


class ComponentParser extends Parser implements ParserInterface
{
    private const TERMINATOR = '/';
    private const OPEN_TAG = '<';
    private const CLOSE_TAG = '>';
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

    public function doComponents(string $rule = "[A-Z]\w+"): void
    {

        $list = [];
        $i = 0;
        $text = $this->html;
        $text .= "\n<Eof />";
        $parentIds = [];
        $depth = 0;
        $parentIds[$depth] = -1;
        $allTags = [];

        $re = <<< REGEX
        /<\/?({$rule})((\s|.*?)*)\/?>|<\/?>/
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

            $allTags[] = $tag;
            $i++;
        }

        $this->depths[$depth] = 1;

        $l = count($allTags);
        $i = 0;
        $isFinished = false;
        $spinner = 0;
        $spinnerMax = $l;
        $isSpinning = false;
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

            if ($this->isClosedTag($tag) && $tag['name'] !== 'Eof') {
                $item = $this->makeTag($tag, $parentIds, $depth, false);
                $list[$item['id']] = $item;
                unset($allTags[$i]);

                $i++;

                continue;
            }

            if ($this->isCloseTag($tag)) {
                $depth--;
            }

            if ($i + 1 < $l) {
                $nextMatch = $allTags[$i + 1];

                if (!$this->isCloseTag($tag) && $this->isCloseTag($nextMatch)) {
                    $item = $this->makeTag($tag, $parentIds, $depth, true);
                    $closer = $this->makeTag($nextMatch, $parentIds, $depth, false, true);

                    if ($item['name'] !== $closer['name']) {
                        $item['hasCloser'] = false;
                        $list[$item['id']] = $item;
                        unset($allTags[$i]);
                        $this->depths[$depth] = 1;
                        $i++;

                        continue;
                    }

                    $closer['contents'] = [];
                    $closer['parentId'] = $item['id'];
                    $closer['contents']['startsAt'] = $item['endsAt'] + 1; // uniqid();
                    $closer['contents']['endsAt'] = $closer['startsAt'] - 1; // uniqid();
                    $contents = substr($this->html, $closer['contents']['startsAt'], $closer['contents']['endsAt'] - $closer['contents']['startsAt'] + 1);
                    $closer['contents']['text'] = '!#base64#' . base64_encode($contents);

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
                    $this->idListByDepth[] = $match['id'];
                }
            }
        }

        $this->list = $list;
    }

    protected function isClosedTag(array $tag): bool
    {
        $text = $tag['text'];
        if (empty($text) || $tag['name'] === 'Fragment') {
            return false;
        }

        return substr($text, -2) === self::TERMINATOR . self::CLOSE_TAG;
    }

    protected function makeTag($tag, $parentIds, $depth, $hasCloser, $isCloser = false): array
    {
        $text = $tag['text'];
        $name = $tag['name'];

        $i = count($this->list);
        $item = [];

        $fqName = '';
        if (is_object($this->component)) {
            $fqName = $this->component->getFullyQualifiedFunction();
        }

        $item['id'] = $tag['id'];
        $item['name'] = empty($name) ? 'Fragment' : $name;
        $item['text'] = $text;
        $item['startsAt'] = $tag['startsAt'];
        $item['endsAt'] = $tag['endsAt'];
        if (!$isCloser) {
            $item['uid'] = Crypto::createOID();
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

    protected function isCloseTag(array $tag): bool
    {
        $text = $tag['text'];
        if (empty($text) || $text === '<>') {
            return false;
        }
        return substr($text, 0, 2) === self::OPEN_TAG . self::TERMINATOR;
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
}
