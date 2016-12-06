<?php

namespace Fusani\Movies\Domain\Model\Movie;

class Episode
{
    protected $id;
    protected $cast;
    protected $crew;
    protected $episode;
    protected $firstAired;
    protected $plot;
    protected $poster;
    protected $season;
    protected $sources;
    protected $title;

    public function __construct($id, $title, $firstAired, $season, $episode)
    {
        $this->cast = [];
        $this->crew = [];
        $this->episode = $episode;
        $this->firstAired = $firstAired;
        $this->id = $id;
        $this->season = $season;
        $this->sources = [];
        $this->title = $title;
    }

    public function addCast(Cast $cast)
    {
        $interest = $cast->provideCastInterest();

        foreach ($this->cast as $existingCast) {
            $existingInterest = $existingCast->provideCastInterest();

            if ($existingInterest['actor'] == $interest['actor']
                || $existingInterest['character'] == $interest['character']
            ) {
                return $this;
            }
        }

        $this->cast[] = $cast;
        return $this;
    }

    public function addCrew(Crew $crew)
    {
        $interest = $crew->provideCrewInterest();

        foreach ($this->crew as $existingCrew) {
            $existingInterest = $existingCrew->provideCrewInterest();

            if ($interest['name'] == $existingInterest['name']) {
                return $this;
            }
        }

        $this->crew[] = $crew;
        return $this;
    }

    public function addSource($type, $name, $link, array $details = [])
    {
        if (!empty($this->sources[$type])) {
            foreach ($this->sources[$type] as $source) {
                $interest = $source->provideSourceInterest();

                if ($interest['name'] == $name && $interest['link'] == $link) {
                    return $this;
                }
            }
        }

        $source = new Source($type, $name, $link, $details);
        $this->sources[$type][] = $source;
        return $this;
    }

    public function identity()
    {
        return sprintf('s%02de%02d-%d', $this->season, $this->episode, $this->id);
    }

    public function provideEpisodeInterest()
    {
        $sources = [];

        foreach ($this->sources as $type => $sourceList) {
            foreach ($sourceList as $source) {
                $sources[$type][] = $source->provideSourceInterest();
            }
        }

        $cast = array_map(function ($c) {
            return $c->provideCastInterest();
        }, $this->cast);

        $crew = array_map(function ($c) {
            return $c->provideCrewInterest();
        }, $this->crew);

        return [
            'id' => $this->id,
            'cast' => $cast,
            'crew' => $crew,
            'episode' => $this->episode,
            'firstAired' => $this->firstAired,
            'plot' => $this->plot,
            'poster' => $this->poster,
            'season' => $this->season,
            'sources' => $sources,
            'title' => $this->title,
        ];
    }

    public function setPlot($plot)
    {
        $this->plot = $plot;
    }

    public function setPoster($poster)
    {
        $this->poster = $poster;
    }

    public function title()
    {
        return $this->title;
    }
}
