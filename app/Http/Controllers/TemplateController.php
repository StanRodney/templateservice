<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use Illuminate\Support\Facades\Cache;

class TemplateController extends Controller
{
    public function index(Request $req)
    {
        $perPage = (int) $req->query('limit', 20);
        $page = (int) $req->query('page', 1);

        $q = Template::query();

        if ($code = $req->query('code')) $q->where('code', $code);
        if ($lang = $req->query('language')) $q->where('language', $lang);

        $p = $q->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $p->items(),
            'meta' => [
                'total' => $p->total(),
                'limit' => $p->perPage(),
                'page' => $p->currentPage(),
                'total_pages' => $p->lastPage(),
                'has_next' => $p->hasMorePages(),
                'has_previous' => $p->currentPage() > 1,
            ]
        ]);
    }

    public function show(Request $req, string $code)
    {
        $lang = $req->query('lang', 'en');
        $version = $req->query('version', 'latest');

        $cacheKey = "template:{$code}:{$lang}:{$version}";

        $template = Cache::remember($cacheKey, 300, function() use ($code, $lang, $version) {
            $q = Template::where('code', $code)
                ->where('language', $lang)
                ->where('active', true);

            if ($version === 'latest') {
                return $q->orderByDesc('version')->first();
            }

            return $q->where('version', (int)$version)->first();
        });

        if (! $template) {
            return response()->json(['success' => false, 'message' => 'Template not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'code' => $template->code,
                'language' => $template->language,
                'title' => $template->title,
                'body' => $template->body,
                'version' => $template->version,
                'metadata' => $template->metadata,
            ]
        ]);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'code' => 'required|string',
            'language' => 'nullable|string',
            'title' => 'nullable|string',
            'body' => 'required|string',
            'version' => 'nullable|integer',
            'active' => 'nullable|boolean',
            'metadata' => 'nullable|array',
        ]);

        $data['language'] = $data['language'] ?? 'en';

        if (empty($data['version'])) {
            $latest = Template::where('code', $data['code'])->max('version');
            $data['version'] = ($latest ? $latest + 1 : 1);
        }

        $template = Template::create($data);

        Cache::forget("template:{$template->code}:{$template->language}:latest");

        return response()->json(['success' => true, 'data' => $template], 201);
    }

    public function update(Request $req, int $id)
    {
        $template = Template::findOrFail($id);

        $data = $req->validate([
            'title' => 'nullable|string',
            'body' => 'nullable|string',
            'active' => 'nullable|boolean',
            'metadata' => 'nullable|array',
        ]);

        $template->update($data);

        Cache::forget("template:{$template->code}:{$template->language}:latest");

        return response()->json(['success' => true, 'data' => $template]);
    }

    public function destroy(int $id)
    {
        $template = Template::findOrFail($id);
        $template->delete();
        Cache::forget("template:{$template->code}:{$template->language}:latest");

        return response()->json(['success' => true, 'message' => 'deleted']);
    }
}
