<?php

namespace Ephect\Components\Generators;

use Ephect\Components\ComponentEntity;
use Ephect\Components\ComponentInterface;
use Ephect\Components\ComponentStructure;

class ComponentDocument
{
    protected $list = [];
    protected $text = '';
    protected $count = 0;
    protected $matches = [];
    protected $currentMatchKey = -1;
    protected $match = null;
    protected $depths = [];
    protected $matchesByDepth = [];
    protected $matchesById = [];
    protected $matchesByKey = [];
    protected $offsetsById = [];
    protected $component = null;

    public function __construct(ComponentInterface $comp)
    {
        $this->component = $comp;
        $this->text = $comp->getCode();
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getList(): array
    {
        return $this->list;
    }
    public function getMatches(): array
    {
        return $this->matches;
    }

    public function getMatchById(int $id): ?ComponentEntity
    {
        $match = null;

        if (!isset($this->list[$id])) {
            return $match;
        }

        $struct = new ComponentStructure($this->list[$id]);

        $match = new ComponentEntity($struct);

        return $match;
    }

    public function getCount(): int
    {
        return $this->count;
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
        return $this->matchesByDepth;
    }

    public function getIDsOfMatches(): array
    {
        return $this->matchesById;
    }

    public function getKeysOfMatches(): array
    {
        return $this->matchesByKey;
    }

    public function matchAll(): bool
    {
        $parser = new ComponentParser($this->component);

        $parser->doComponents();
        $this->list = $parser->getList();
        $this->depths = $parser->getDepths();

        $this->matchesByDepth = $parser->getIdListByDepth();
        $this->matchesById = $this->sortMatchesById();
        $this->matchesByKey = $this->sortMatchesByKey();

        $this->count = count($this->list);

        return ($this->count > 0);
    }

    public function sortMatchesByDepth(): array
    {
        $maxDepth = count($this->depths);
        $result = [];
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

    public function resetMatchId(): void
    {
        $this->currentMatchKey = -1;
    }

    public function getCurrentMatch(): ?ComponentEntity
    {
        $currentId = $this->matchesById[$this->currentMatchKey];
        if ($this->match === null || $this->match->getId() !== $currentId) {
            $struct =  new ComponentStructure($this->list[$currentId]);
            $this->match = new ComponentEntity($struct);
        }

        return $this->match;
    }

    public function getNextMatch(): ?ComponentEntity
    {
        $this->currentMatchKey++;

        $this->match = null;
        if ($this->currentMatchKey == $this->count) {
            return null;
        }

        return $this->getCurrentMatch();
    }

    public function replaceMatches(ComponentDocument $doc, string &$childText): string
    {
        $parentMatchesById = $this->getIDsOfMatches();
        $parentCount = $this->getCount();
        $parentText = $this->text;
        $parentReplacing = '';

        $childMatchesById = $doc->getIDsOfMatches();
        $childCount = $doc->getCount();
        $childText = $childText;

        for ($i = $parentCount - 1; $i > -1; $i--) {
            $parentId = $parentMatchesById[$i];
            $parentMatch = $this->getMatchById($parentId);
            $parentReplaced = '';

            if ($parentMatch->getName() !== 'Slot') {
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
                    $childMatch->getName() !== 'Slot'
                    || $parentMatch->props('name') !== $childMatch->props('name')
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

        return $parentText;
    }

    public function replaceThisMatch(ComponentEntity $match, string $text, string $replacing): string
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

            $parentReplaced = substr($text, $start, $length);

            $text = str_replace($parentReplaced, $replacing, $text);
        } else {
            $offset = strlen($replacing) - strlen($match->getText());
            $this->offsetsById[$match->getId()] = $offset;

            $text = str_replace($match->getText(), $replacing, $text);
        }

        return $text;
    }

    protected function findOffset(int $parentId): int
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
}
