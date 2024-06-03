<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\KbArticle;
use App\Models\KbCategory;
use App\Models\Ticket;

class SupportController extends Controller
{
    public function tickets()
    {
        return view('admin.ticket.index');
    }

    public function createTicket()
    {
        return view('admin.ticket.create');
    }
    
    public function ticket($id)
    {
        return view('admin.ticket.show', ['id' => $id, 'ticket' => Ticket::find($id)]);
    }
    
    public function kbCategories()
    {
        return view('admin.kb.index');
    }
    
    public function createKbCategory()
    {
        return view('admin.kb.create');
    }
    
    public function kbCategory($category_id)
    {
        return view('admin.kb.show', ['category_id' => $category_id, 'category' => KbCategory::find($category_id)]);
    }
    
    public function createKbArticle($category_id)
    {
        return view('admin.kb.article.create', ['category_id' => $category_id]);
    }
    
    public function kbArticle($category_id, $article_id)
    {
        return view('admin.kb.article.show', ['category_id' => $category_id, 'article_id' => $article_id, 'article' => KbArticle::find($article_id)]);
    }

    public function announcements()
    {
        return view('admin.announcement.index');
    }

    public function createAnnouncement()
    {
        return view('admin.announcement.create');
    }

    public function announcement($id)
    {
        return view('admin.announcement.show', ['id' => $id, 'announcement' => Announcement::find($id)]);
    }
}
