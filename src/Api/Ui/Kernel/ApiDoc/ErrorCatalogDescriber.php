<?php

declare(strict_types=1);

namespace App\Api\Ui\Kernel\ApiDoc;

use Nelmio\ApiDocBundle\Describer\DescriberInterface;
use OpenApi\Annotations\OpenApi;
use OpenApi\Undefined;
use App\Api\Ui\Kernel\ExceptionTransformer\ErrorCatalog;
use App\Api\Ui\Kernel\ExceptionTransformer\ErrorCatalogEntry;
use App\Shared\Common\Functional;

/**
 * Appends the full error catalogue (generated from ErrorCatalog) to the documentation
 * description, so the listed errors can never drift from the ones the API actually returns.
 * Registered only for the merchant-business area.
 */
readonly class ErrorCatalogDescriber implements DescriberInterface
{
    public function __construct(private ErrorCatalog $catalog) {}

    public function describe(OpenApi $api): void
    {
        $existing = Undefined::UNDEFINED === $api->info->description ? '' : $api->info->description;
        $api->info->description = trim($existing . "\n\n" . $this->renderCatalog());
    }

    private function renderCatalog(): string
    {
        $entries = $this->catalog->all();
        usort(
            $entries,
            static fn (ErrorCatalogEntry $a, ErrorCatalogEntry $b): int => [$a->status, $a->type->value] <=> [
                $b->status,
                $b->type->value,
            ],
        );

        $rows = Functional::map(
            static fn (ErrorCatalogEntry $entry): string => sprintf(
                '<tr><td>%d</td><td><code>%s</code></td><td><code>%s</code></td><td>%s</td></tr>'
                    . '<tr><td colspan="4">%s</td></tr>',
                $entry->status,
                htmlspecialchars($entry->type->value, ENT_QUOTES),
                htmlspecialchars($entry->instance->value, ENT_QUOTES),
                htmlspecialchars($entry->title, ENT_QUOTES),
                htmlspecialchars($entry->detail, ENT_QUOTES),
            ),
            $entries,
        );

        $table = '<table>' . "\n"
            . '<thead><tr><th>HTTP</th><th>Type</th><th>Instance</th><th>Title</th></tr></thead>' . "\n"
            . '<tbody>' . "\n" . implode("\n", $rows) . "\n" . '</tbody>' . "\n"
            . '</table>';

        return implode("\n\n", [
            '## Errors',
            'All error responses share the same shape (`type`, `title`, `detail`, `instance`). '
                . 'This is the complete catalogue of errors this API can return:',
            $table,
        ]);
    }
}
