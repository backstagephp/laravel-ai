<?php

namespace Backstage\Laravel\AI\Drivers\ElevenLabs;

use Backstage\Laravel\AI\Contracts\HasAgents;
use Backstage\Laravel\AI\Contracts\HasConversations;
use Backstage\Laravel\AI\Contracts\HasWorkflows;
use Backstage\Laravel\AI\Drivers\ElevenLabs\Clients\ElevenLabsClient;
use Backstage\Laravel\AI\Drivers\ElevenLabs\Concerns\InteractsWithAgents;
use Backstage\Laravel\AI\Drivers\ElevenLabs\Concerns\InteractsWithConversations;
use Backstage\Laravel\AI\Drivers\ElevenLabs\Concerns\InteractsWithWorkflows;

/**
 * @mixin ElevenLabsClient
 */
class ElevenLabsDriver implements HasAgents, HasConversations, HasWorkflows
{
    use InteractsWithAgents;
    use InteractsWithConversations;
    use InteractsWithWorkflows;

    public function __construct(protected ElevenLabsClient $client) {}
}
