<?php

namespace Ephect\Components\Generators;

use Ephect\Components\ComponentEntity;
use Ephect\Components\ComponentStructure;
use Ephect\Components\PreHtml;

class ComponentDocument
{
    private $_list = [];
    private $_text = '';
    private $_count = 0;
    private $_matches = [];
    private $_currentMatchKey = -1;
    private $_match = null;
    private $_depths = [];
    private $_matchesByDepth = [];
    private $_matchesById = [];
    private $_matchesByKey = [];
    private $_offsetsById = [];

    public function __construct(string $text)
    {
        $this->_text = $text;
    }

    public function getText(): string
    {
        return $this->_text;
    }

    public function getList(): array
    {
        return $this->_list;
    }
    public function getMatches(): array
    {
        return $this->_matches;
    }

    public function getMatchById(int $id): ?ComponentEntity
    {
        $match = null;

        if (!isset($this->_list[$id])) {
            return $match;
        }

        $struct = new ComponentStructure($this->_list[$id]);

        $match = new ComponentEntity($struct);

        return $match;
    }

    public function getCount(): int
    {
        return $this->_count;
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

    public function matchAll(): bool
    {
        $prehtml = new PreHtml($this->_text);
        $parser = new ComponentParser($prehtml);

        $this->_list = $parser->doComponents();
        $this->_list = $this->getList();
        $this->_depths = $parser->getDepths();

        $this->_matchesByDepth = $parser->getIdListByDepth(); //$this->sortMatchesByDepth();
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

    public function resetMatchId(): void
    {
        $this->_currentMatchKey = -1;
    }

    public function getCurrentMatch(): ?ComponentEntity
    {
        $currentId = $this->_matchesById[$this->_currentMatchKey];
        if ($this->_match === null || $this->_match->getId() !== $currentId) {
            $struct =  new ComponentStructure($this->_list[$currentId]);
            $this->_match = new ComponentEntity($struct, $this);
        }

        return $this->_match;
    }

    public function getNextMatch(): ?ComponentEntity
    {
        $this->_currentMatchKey++;

        $this->_match = null;
        if ($this->_currentMatchKey == $this->_count) {
            return null;
        }

        return $this->getCurrentMatch();
    }

    public function replaceMatches(ComponentDocument $doc, string &$childText): string
    {
        $parentMatchesById = $this->getIDsOfMatches();
        $parentCount = $this->getCount();
        $parentText = $this->_text;
        $parentReplacing = '';

        $childMatchesById = $doc->getIDsOfMatches();
        $childCount = $doc->getCount();
        $childText = $childText;

        for ($i = $parentCount - 1; $i > -1; $i--) {
            $parentId = $parentMatchesById[$i];
            $parentMatch = $this->getMatchById($parentId);
            $parentReplaced = '';

            if ($parentMatch->getMethod() !== 'Block') {
                continue;
            }

            if ($parentMatch->hasCloser()) {
                $start = $parentMatch->getStart();
                $closer = $parentMatch->getCloser();
                $length = $closer['endsAt'] - $parentMatch->getStart() + 1;

                $parentReplaced = substr($parentText, $start, $length);

                $start = $closer['contents']['startsAt'];
                $length = $closer['contents']['endsAt'] - $start + 1;

                $parentReplacing = substr($parentText, $start, $length);
            } else {
                $parentReplaced = $parentMatch->getText();
            }
            $matchesChildBlock = false;

            for ($j = $childCount - 1; $j > -1; $j--) {
                $childId = $childMatchesById[$j];
                $childMatch = $doc->getMatchById($childId);
                $childReplacing = '';

                if (
                    $childMatch->getMethod() !== 'Block'
                    || $parentMatch->properties('name') !== $childMatch->properties('name')
                ) {
                    continue;
                }

                $matchesChildBlock = true;
                if ($childMatch->hasCloser()) {
                    $closer = $childMatch->getCloser();
                    $start = $closer['contents']['startsAt'];
                    $length = $closer['contents']['endsAt'] - $start + 1;

                    $childReplacing = substr($childText, $start, $length);

                    $start = $childMatch->getStart();
                    $closer = $childMatch->getCloser();
                    $length = $closer['endsAt'] - $childMatch->getStart() + 1;
    
                    $childReplaced = substr($childText, $start, $length);

                } else {
                    $childReplacing = $childMatch->getText();
                }

                $childText = str_replace($childReplaced, '', $childText);
                $parentText = str_replace($parentReplaced, $childReplacing, $parentText);

                break;
            }

            if(!$matchesChildBlock) {
                $parentText = str_replace($parentReplaced, $parentReplacing, $parentText);
            }
        }

    
        // $parentText = str_replace($this->_endOfFile, '', $parentText);

        return $parentText;
    }

    public function replaceThisMatch(ComponentEntity $match, string $text, string $replacing): string
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

            $parentReplaced = substr($text, $start, $length);

            $text = str_replace($parentReplaced, $replacing, $text);
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
}
