<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use App\Support\Content;
use App\Support\ContentSchema;
use App\Support\Icons;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(private ContentSchema $schema, private Content $content) {}

    public function edit(string $screen): View
    {
        abort_unless($this->schema->has($screen), 404);

        return view('admin.content.edit', [
            'key' => $screen,
            'screen' => $this->schema->screen($screen),
            'values' => $this->content->get($screen),
            'icons' => array_keys(Icons::PATHS),
            'customised' => SiteContent::where('key', $screen)->value('updated_at'),
        ]);
    }

    public function update(Request $request, string $screen): RedirectResponse
    {
        abort_unless($this->schema->has($screen), 404);

        $request->validate($this->schema->rules($screen), [], $this->schema->attributes($screen));

        $values = $this->schema->normalize(
            $screen,
            (array) $request->input('fields', []),
            (array) $request->file('uploads', []),
            (array) $request->input('remove', []),
        );

        $this->content->save($screen, $values);

        return redirect()
            ->route('admin.content.edit', $screen)
            ->with('status', 'Saved. The website has been updated.');
    }

    /** Restore the original text and photos for a screen. */
    public function destroy(string $screen): RedirectResponse
    {
        abort_unless($this->schema->has($screen), 404);

        SiteContent::where('key', $screen)->delete();
        $this->content->flush();

        return redirect()
            ->route('admin.content.edit', $screen)
            ->with('status', 'Restored to the original content.');
    }
}
