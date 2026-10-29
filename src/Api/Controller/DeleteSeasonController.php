<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use HuseyinFiliz\Pickem\Season;

class DeleteSeasonController extends AbstractDeleteControllerWithRelationCheck
{
    protected function getModelClass(): string
    {
        return Season::class;
    }

    protected function getRelationName(): string
    {
        return 'weeks';
    }

    protected function getErrorMessageKey(): string
    {
        // GÜNCELLENDİ: lib.validation.errors.season_in_use -> lib.messages.in_use
        return 'huseyinfiliz-pickem.lib.messages.in_use';
    }
}
