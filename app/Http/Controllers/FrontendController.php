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
        return view('frontend.home', [
            'portfolios' => Portfolio::query()->latest('id')->take(6)->get(),
            'posts' => Post::query()->published()->latest('published_at')->take(3)->get(),
            'products' => $this->marketplaceProducts(),
            'categories' => $this->marketplaceCategories(),
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
     * Product list shared by the marketplace page and the home-page section.
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
                    'stock' => (int) $product->stock,
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
