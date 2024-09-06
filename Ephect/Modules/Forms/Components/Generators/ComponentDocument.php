<?php

namespace Ephect\Modules\Forms\Components\Generators;

use Ephect\Modules\Forms\Components\ComponentEntity;
use Ephect\Modules\Forms\Components\ComponentInterface;
use Ephect\Modules\Forms\Components\ComponentStructure;

class ComponentDocument
{
    protected array $list = [];
    protected ?string $text = '';
    protected int $count = 0;
    protected array $matches = [];
    protected int $currentMatchKey = -1;
    protected ?ComponentEntity $match = null;
    protected array $depths = [];
    protected array $matchesByDepth = [];
    protected array $matchesById = [];
    protected array $matchesByKey = [];
    protected array $offsetsById = [];
    protected ?ComponentInterface $component = null;

    public function __construct(ComponentInterface $comp)
    {
        $this->component = $comp;
        $this->text = $comp->getCode();
    }

    public function getMatches(): array
    {
        return $this->matches;
    }

    public function fieldValue(int $i, string $field, string $value): void
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

    public function getList(): array
    {
        return $this->list;
    }

    public function sortMatchesById(): array
    {
        $result = [];
        foreach ($this->list as $match) {
            $result[] = $match['id'];
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

    public function resetMatchId(): void
    {
        $this->currentMatchKey = -1;
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

    public function getCurrentMatch(): ?ComponentEntity
    {
        $currentId = $this->matchesById[$this->currentMatchKey];
        if ($this->match === null || $this->match->getId() !== $currentId) {
            $struct = new ComponentStructure($this->list[$currentId]);
            $this->match = new ComponentEntity($struct);
        }

        return $this->match;
    }

    public function replaceMatches(ComponentDocument $doc, string &$childText): string
    {
        $parentMatchesById = $this->getIDsOfMatches();
        $parentCount = $this->getCount();
        $parentText = $this->text;
        $parentReplacing = '';

        $childMatchesById = $doc->getIDsOfMatches();
        $childCount = $doc->getCount();

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

            if (!$matchesChildBlock) {
                $parentText = str_replace($parentReplaced, $parentReplacing, $parentText);
            }
        }

        return $parentText;
    }

    public function getIDsOfMatches(): array
    {
        return $this->matchesById;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getMatchById(int $id): ?ComponentEntity
    {
        if (!isset($this->list[$id])) {
            return null;
        }

        $struct = new ComponentStructure($this->list[$id]);

        return new ComponentEntity($struct);
    }

    public function getText(): string
    {
        return $this->text;
    }

}
