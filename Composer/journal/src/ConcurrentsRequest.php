<?php

namespace Remp\Journal;

class ConcurrentsRequest
{
    protected $filterBy = [];

    protected $groupBy = [];

    protected $timeBefore;

    protected $timeAfter;

    public function addFilter(string $tag, string ...$values)
    {
        foreach ($values as &$v) {
            $v = strval($v);
        }
        $this->filterBy[] = [
            "tag" => $tag,
            "values" => $values,
        ];
    }

    public function addGroup(string ...$tags)
    {
        $this->groupBy = array_merge($this->groupBy, $tags);
    }

    public function setTimeBefore(\DateTime $timeBefore)
    {
        $this->timeBefore = $timeBefore;
    }

    public function setTimeAfter(\DateTime $timeAfter)
    {
        $this->timeAfter = $timeAfter;
    }

    public function getFilterBy(): array
    {
        return $this->filterBy;
    }

    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    public function getTimeBefore(): ?\DateTime
    {
        return $this->timeBefore;
    }

    public function getTimeAfter(): ?\DateTime
    {
        return $this->timeAfter;
    }
}
