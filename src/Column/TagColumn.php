<?php

namespace Glint\Glint\Column;

use Illuminate\Support\HtmlString;

class TagColumn extends Column
{
    private string $delimiter = ',';

    private array $tags = [];

    public function stableColors($tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function delimitBy(string $delimiter): static
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function getState($record)
    {
        $state = parent::getState($record);

        if (empty($state)) {
            return '';
        }

        $vals = explode($this->delimiter, $state);

        $tag_string = '';
        foreach ($vals as $tag) {
            if (! in_array($tag, $this->tags)) {
                $this->tags[] = $tag;
            }

            $tagIdentifier = array_search($tag, $this->tags) ?: 'default';
            $tag_string .= "<div class=\"data-tag-{$tagIdentifier}\"><span>{$tag}</span></div>";
        }

        return new HtmlString("<div class=\"data-tags\">$tag_string</div>");
    }
}
