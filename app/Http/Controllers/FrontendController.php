<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Portfolio;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function home(): View
    {
        $displayProjects = $this->portfolioGroup('display');
        $apps = $this->portfolioGroup('software');

        return view('frontend.home', [
            'displayProjects' => $displayProjects,
            'apps' => $apps,
            'iotProjects' => $this->portfolioGroup('iot'),
            // Counts follow the data, so they stay true as projects are added.
            'facts' => [
                ['value' => count(config('company.services', [])), 'label' => 'Lini layanan Total IT Solution'],
                ['value' => $apps->count(), 'label' => 'Aplikasi untuk instansi & publik'],
                ['value' => $displayProjects->count(), 'label' => 'Proyek control room & display'],
            ],
            'posts' => Post::query()->published()->latest('published_at')->take(3)->get(),
        ]);
    }

    public function blog(): View
    {
        return view('frontend.blog', [
            'posts' => Post::query()->published()->latest('published_at')->paginate(9),
        ]);
    }

    public function blogShow(string $slug): View
    {
        $post = Post::query()->published()->where('slug', $slug)->firstOrFail();

        // Content comes from Filament's RichEditor (HTML). Sanitize before output
        // so a compromised or careless admin account cannot inject scripts.
        $content = str((string) $post->content)->sanitizeHtml();

        return view('frontend.blog-show', compact('post', 'content'));
    }

    public function portfolio(): View
    {
        return view('frontend.portfolio', [
            'portfolios' => Portfolio::query()->latest('id')->paginate(9),
        ]);
    }

    public function marketplace(): View
    {
        return view('frontend.marketplace', [
            'products' => $this->marketplaceProducts(),
            'categories' => $this->marketplaceCategories(),
        ]);
    }

    /**
     * Home-page project cards for one portfolio group, managed in the admin
     * panel. Falls back to the bundled Company Profile content while the
     * table has nothing for that group, so a section is never empty.
     *
     * @return Collection<int, array{title: string, category: ?string, subtitle: ?string, desc: ?string, image: ?string, url: ?string}>
     */
    private function portfolioGroup(string $group): Collection
    {
        $items = Portfolio::query()->inGroup($group)->get()->map(fn (Portfolio $p): array => [
            'title' => $p->title,
            'category' => $p->category,
            'subtitle' => $p->subtitle,
            'desc' => $p->description,
            'image' => $p->image_url,
            'url' => $p->url,
        ]);

        if ($items->isNotEmpty()) {
            return $items;
        }

        $img = fn (string $file): string => asset('images/profile/'.$file);

        return collect(match ($group) {
            'display' => array_map(fn (array $p): array => ['title' => $p['client'], 'category' => $p['type'], 'subtitle' => $p['tech'], 'desc' => null, 'image' => $img($p['image']), 'url' => null], config('company.display.projects', [])),
            'software' => array_map(fn (array $a): array => ['title' => $a['name'], 'category' => null, 'subtitle' => $a['client'], 'desc' => $a['desc'], 'image' => $img($a['image']), 'url' => $a['url'] ?? null], config('company.apps', [])),
            'iot' => array_map(fn (array $p): array => ['title' => $p['title'], 'category' => null, 'subtitle' => null, 'desc' => $p['desc'], 'image' => $img($p['image']), 'url' => null], config('company.iot.projects', [])),
            default => [],
        });
    }

    /**
     * Categories that actually have products, for the marketplace filter.
     */
    private function marketplaceCategories(): Collection
    {
        return Category::query()
            ->whereHas('products')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }

    /**
     * Product list for the marketplace page.
     *
     * The description field is expected in the form:
     *   "Summary paragraph\n\nKey: Value\nKey: Value"
     * The first paragraph becomes the summary and "Key: Value" lines become specs.
     */
    private function marketplaceProducts(): Collection
    {
        return Product::query()
            ->with('category:id,name,slug')
            ->latest('id')
            ->get()
            ->map(function (Product $product): array {
                [$summary, $specs] = $this->parseDescription((string) $product->description);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'cat' => $product->category?->slug ?? 'other',
                    'catLabel' => $product->category?->name ?? 'Lainnya',
                    'price' => (int) $product->price,
                    'status' => (string) $product->status,
                    'desc' => $summary,
                    'specs' => $specs,
                    'brand' => $product->brand,
                    'stock' => max(0, (int) $product->stock),
                    'image' => $product->image_url,
                ];
            })
            ->values();
    }

    /**
     * @return array{0: string, 1: array<string, string>}
     */
    private function parseDescription(string $description): array
    {
        $parts = preg_split("/\R\R/", trim($description), 2);
        $summary = trim($parts[0] ?? '');
        $specs = [];

        foreach (preg_split("/\R/", trim($parts[1] ?? '')) ?: [] as $line) {
            if (! str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode(':', $line, 2));

            if ($key !== '') {
                $specs[$key] = $value;
            }
        }

        return [$summary, $specs];
    }
}
