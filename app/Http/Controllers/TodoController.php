<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::query()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($todo){
                return [
                    'id' => $todo->id,
                    'title' => $todo->title,
                    'done' => $todo->done === 1,
                    'created_at_diff' => $todo->created_at->diffForHumans()
                ];
            });

        return response()->json([
            'todos' => $todos
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => ['required', 'unique:todos,title']
        ]);

        $title = $request->input('title');

        
        $todo = Todo::query()
            ->create([
                'title' => $title
            ]);

        return response()->json([
            'success' => true,
            'todo' => $todo
        ]);
    }

    public function update_status()
    {

        request()->validate([
            'id' => ['required', 'exists:todos,id']
        ]);
        
        $id = request()->input('id');
        
        $todo = Todo::query()
            ->where('id', '=', $id)
            ->firstOrFail();
        
        $todo = Todo::query()
            ->where('id', '=', $id)
            ->update([
                'done' => !$todo->done
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function destroy()
    {
        request()->validate([
            'id' => ['required', 'exists:todos,id']
        ]);
        
        $id = request()->input('id');
        
        Todo::query()
            ->where('id', '=', $id)
            ->delete();
        
        return response()->json([
            'success' => true
        ]);
    }
}
