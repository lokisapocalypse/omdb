<?php

namespace Fusani\Movies\Domain\Model\Movie;

interface MovieRepository
{
    public function manyWithTitleLike($title);
    public function oneOfId($id);
}
