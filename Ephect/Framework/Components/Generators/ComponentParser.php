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

    public function doComponents(): void
    {
        $result = [];

        $list = [];

        /**
         * parse only components as defined by JSX
         * $re = '/<\/?([A-Z]\w+)(.*?)>|<\/?>/m';
         */
        // parse all tags comprising HTML ones 

        $re = '/<\/?([A-Z]\w+)((\s|.*?)+)>|<\/?>/m';

        $str = $this->html;

        preg_match_all($re, $str, $list, PREG_OFFSET_CAPTURE | PREG_SET_ORDER, 0);

        $l = count($list);
        $closers = [];
        // Re-structure the list recursively
        for ($i = $l - 1; $i > -1; $i--) {

            $list[$i]['uid'] = Crypto::createUID();

            $list[$i]['id'] = $i;
            $list[$i]['name'] = (!isset($list[$i][1][0])) ? 'Fragment' : $list[$i][1][0];
            $list[$i]['class'] = ComponentRegistry::read($list[$i]['name']);
            $list[$i]['component'] = $this->component->getFullyQualifiedFunction();
            $list[$i]['text'] = $list[$i][0][0];
            $list[$i]['method'] = 'echo';
            $list[$i]['startsAt'] = $list[$i][0][1];
            $list[$i]['endsAt'] = $list[$i][0][1] + strlen($list[$i][0][0]);
            $list[$i]['props'] = ($list[$i]['name'] === 'Fragment') ? [] : $this->doArguments($list[$i][2][0]);
            $list[$i]['node'] = false;
            $list[$i]['hasCloser'] = false;
            $list[$i]['isCloser'] = false;

            if ($list[$i][0][0][1] === '/') {
                for ($j = $i - 1; $j > -1; $j--) {
                    if (
                        ($list[$i][0][0] === '</>' && $list[$j][0][0] === '<>')
                        || (isset($list[$i][1]) && $list[$i][1][0] === $list[$j][1][0])
                    ) {

                        $currentCloser = [
                            'id' => $i,
                            'parentId' => $j,
                            'text' => $list[$i][0][0],
                            'startsAt' => $list[$i][0][1],
                            'endsAt' => $list[$i][0][1] + strlen($list[$i][0][0]),
                            'contents' => ['startsAt' => $list[$j][0][1] + strlen($list[$j][0][0]), 'endsAt' => $list[$i][0][1] - 1],
                        ];

                        if (isset($list[$i][1]) && $list[$i][1][0] == 'Slot') {
                            array_unshift($closers, $currentCloser);
                        } else {
                            array_push($closers, $currentCloser);
                        }
                        
                        $list[$i]['isCloser'] = true;
                        $list[$i]['method'] = 'render';

                        break;
                    }
                }
            }

            if (isset($list[$i])) {
                unset($list[$i][0]);
                unset($list[$i][1]);
                unset($list[$i][2]);
                unset($list[$i][3]);
            }

        }

        $l = count($list);

        for ($i = 0; $i < $l; $i++) {

            $component = $list[$i]['text'];
            $compCloserText = '</' . ($list[$i]['name']  == 'Fragment' ? '' : $list[$i]['name']) . '>';

            if ($component[strlen($component) - 2] !== '/' && $component[1] !== '/') {
                $n = count($closers);

                for ($j = 0; $j < $n; $j++) {

                    $curCloserText = $closers[$j]['text'];
                    if ($curCloserText == $compCloserText) {
                        $closer = $closers[$j];
                        $closer['parentId'] = $i;
                        $closer['contents']['startsAt'] = $list[$i]['startsAt'] + strlen($list[$i]['text']);
                        $list[$i]['closer'] = $closer;
                        $list[$i]['hasCloser'] = true;
                        $list[$i]['isCloser'] = false;

                        unset($closers[$j]);
                        $closers = array_values($closers);

                        break;
                    }
                }
            }
        }

        $depth = 0;
        $parentIds = [];
        $parentIds[$depth] = -1;

        $l = count($list);

        // Add useful information in list like depth and parentId
        for ($i = 0; $i < $l; $i++) {

            $siblingId = $i - 1;

            $isSibling = isset($list[$siblingId]) && $list[$siblingId]['hasCloser'];

            $component = $list[$i]['text'];
            $firstName = $list[$i]['name'];
            $secondName = isset($list[$i + 1]) ? $list[$i + 1]['name'] : 'eof';

            if (!isset($parentIds[$depth])) {
                $parentIds[$depth] = $i - 1;
            }

            $list[$i]['isSibling'] = $isSibling;
            $list[$i]['parentId'] = $parentIds[$depth];
            $list[$i]['depth'] = $depth;

            if (TERMINATOR . $firstName != $secondName) {
                if ($list[$i]['isCloser']) {
                    $list[$i]['isSibling'] = $isSibling;

                    $pId = !$isSibling && isset($parentIds[$depth]) ? $parentIds[$depth] : $siblingId;
                    $depth--;

                    $list[$i]['parentId'] = $parentIds[$depth];
                    $list[$i]['depth'] = $depth;

                    if ($list[$pId]['isSibling']) {
                        $list[$i]['depth'] = $list[$pId]['depth'];
                    }
                } elseif ($component[1] == QUEST_MARK) {
                } elseif (false === $list[$i]['hasCloser']) {
                } elseif ($component[1] == SKIP_MARK) {
                } else {
                    if ($list[$i]['hasCloser']) {
                        $depth++;
                    }

                    $this->depths[$depth] = 1;

                    if (isset($parentIds[$depth])) {
                        unset($parentIds[$depth]);
                    }
                }
            }
        }

        for ($i = $l - 1; $i > -1; $i--) {
            // Remove useless data
            if ($list[$i]['isCloser']) {
                unset($list[$i]);
            } else {
                unset($list[$i]['isCloser']);
            }
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
    }
}
