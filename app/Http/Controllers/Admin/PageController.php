<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::orderBy('slug')->get()]);
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page(['is_published' => true])]);
    }

    public function store(Request $request)
    {
        Page::create($this->validated($request));

        return redirect()->route('admin.pages.index')->with('status', 'Page created.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $page->update($this->validated($request, $page));

        return redirect()->route('admin.pages.index')->with('status', 'Page updated.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('status', 'Page deleted.');
    }

    private function validated(Request $request, ?Page $page = null): array
    {
        $data = $request->validate([
            'slug' => ['required', 'alpha_dash', 'max:80', Rule::unique('pages')->ignore($page)],
            'title.en' => ['required', 'string', 'max:160'],
            'title.ja' => ['nullable', 'string', 'max:160'],
            'title.vi' => ['nullable', 'string', 'max:160'],
            'body.en' => ['required', 'string', 'max:20000'],
            'body.ja' => ['nullable', 'string', 'max:20000'],
            'body.vi' => ['nullable', 'string', 'max:20000'],
        ]);

        return [
            ...$data,
            'title' => array_filter($data['title']),
            'body' => array_filter($data['body']),
            'is_published' => $request->boolean('is_published'),
        ];
    }
}
