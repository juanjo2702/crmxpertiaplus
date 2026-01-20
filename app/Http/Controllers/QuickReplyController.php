<?php

namespace App\Http\Controllers;

use App\Models\QuickReply;
use App\Models\QuickReplyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class QuickReplyController extends Controller
{
    /**
     * Get all active categories with replies for chat usage
     */
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;

        $categories = QuickReplyCategory::where('tenant_id', $tenantId)
            ->active()
            ->ordered()
            ->with(['activeReplies'])
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'icon' => $category->icon,
                    'replies' => $category->activeReplies->map(function ($reply) {
                        return [
                            'id' => $reply->id,
                            'title' => $reply->title,
                            'body' => $reply->body,
                            'image_url' => $reply->image_url,
                        ];
                    }),
                ];
            });

        return response()->json($categories);
    }

    /**
     * Show management page
     */
    public function manage()
    {
        $tenantId = Auth::user()->tenant_id;

        $categories = QuickReplyCategory::where('tenant_id', $tenantId)
            ->ordered()
            ->with(['replies' => function ($query) {
                $query->orderBy('order');
            }])
            ->get();

        return Inertia::render('Tenant/QuickReplies', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new category
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
        ]);

        $tenantId = Auth::user()->tenant_id;
        $maxOrder = QuickReplyCategory::where('tenant_id', $tenantId)->max('order') ?? 0;

        $category = QuickReplyCategory::create([
            'tenant_id' => $tenantId,
            'name' => $request->name,
            'icon' => $request->icon,
            'order' => $maxOrder + 1,
        ]);

        return response()->json($category);
    }

    /**
     * Update a category
     */
    public function updateCategory(Request $request, QuickReplyCategory $category)
    {
        $this->authorizeCategory($category);

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
        ]);

        $category->update([
            'name' => $request->name,
            'icon' => $request->icon,
        ]);

        return response()->json($category);
    }

    /**
     * Delete a category
     */
    public function destroyCategory(QuickReplyCategory $category)
    {
        $this->authorizeCategory($category);
        $category->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Toggle category active status
     */
    public function toggleCategory(QuickReplyCategory $category)
    {
        $this->authorizeCategory($category);

        $category->update(['is_active' => !$category->is_active]);

        return response()->json($category);
    }

    /**
     * Store a new quick reply
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:quick_reply_categories,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:5120', // 5MB max
        ]);

        $category = QuickReplyCategory::findOrFail($request->category_id);
        $this->authorizeCategory($category);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('quick_replies', 'public');
        }

        $maxOrder = QuickReply::where('category_id', $request->category_id)->max('order') ?? 0;

        $reply = QuickReply::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'body' => $request->body,
            'image_path' => $imagePath,
            'order' => $maxOrder + 1,
        ]);

        return response()->json([
            ...$reply->toArray(),
            'image_url' => $reply->image_url,
        ]);
    }

    /**
     * Update a quick reply
     */
    public function update(Request $request, QuickReply $quickReply)
    {
        $this->authorizeReply($quickReply);

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        $data = [
            'title' => $request->title,
            'body' => $request->body,
        ];

        if ($request->remove_image && $quickReply->image_path) {
            Storage::disk('public')->delete($quickReply->image_path);
            $data['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            if ($quickReply->image_path) {
                Storage::disk('public')->delete($quickReply->image_path);
            }
            $data['image_path'] = $request->file('image')->store('quick_replies', 'public');
        }

        $quickReply->update($data);

        return response()->json([
            ...$quickReply->fresh()->toArray(),
            'image_url' => $quickReply->image_url,
        ]);
    }

    /**
     * Delete a quick reply
     */
    public function destroy(QuickReply $quickReply)
    {
        $this->authorizeReply($quickReply);

        if ($quickReply->image_path) {
            Storage::disk('public')->delete($quickReply->image_path);
        }

        $quickReply->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Toggle quick reply active status
     */
    public function toggle(QuickReply $quickReply)
    {
        $this->authorizeReply($quickReply);

        $quickReply->update(['is_active' => !$quickReply->is_active]);

        return response()->json($quickReply);
    }

    /**
     * Reorder categories
     */
    public function reorderCategories(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:quick_reply_categories,id',
        ]);

        $tenantId = Auth::user()->tenant_id;

        foreach ($request->order as $position => $categoryId) {
            QuickReplyCategory::where('id', $categoryId)
                ->where('tenant_id', $tenantId)
                ->update(['order' => $position]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Reorder replies within a category
     */
    public function reorderReplies(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:quick_reply_categories,id',
            'order' => 'required|array',
            'order.*' => 'integer|exists:quick_replies,id',
        ]);

        $category = QuickReplyCategory::findOrFail($request->category_id);
        $this->authorizeCategory($category);

        foreach ($request->order as $position => $replyId) {
            QuickReply::where('id', $replyId)
                ->where('category_id', $request->category_id)
                ->update(['order' => $position]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Authorize access to category
     */
    private function authorizeCategory(QuickReplyCategory $category)
    {
        if ($category->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'No autorizado');
        }
    }

    /**
     * Authorize access to reply
     */
    private function authorizeReply(QuickReply $reply)
    {
        $this->authorizeCategory($reply->category);
    }
}
