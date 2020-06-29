<?php

namespace Mmerlijn\NovaAgendaPreview;

use Laravel\Nova\ResourceTool;

class AgendaPreview extends ResourceTool
{
    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Nova Agenda Preview';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'nova-agenda-preview';
    }
}
