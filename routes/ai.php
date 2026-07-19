<?php

use App\Mcp\Servers\AiServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('ai', AiServer::class);

Mcp::web('/mcp/ai', AiServer::class);
