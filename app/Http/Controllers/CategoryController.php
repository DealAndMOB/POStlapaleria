<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::query();
            return Datatables::of($categories)
                ->addColumn('action', function ($category) {
                    $editBtn = '<button onclick="editCategory('.$category->id.')" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Editar</button>';
                    $deleteBtn = '<button onclick="deleteCategory('.$category->id.')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Eliminar</button>';
                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('categories.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:categories',
        ]);

        $category = Category::create($validated);

        return response()->json(['success' => true, 'category' => $category]);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $id,
        ]);

        $category->update($validated);

        return response()->json(['success' => true, 'category' => $category]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['success' => true]);
    }
}