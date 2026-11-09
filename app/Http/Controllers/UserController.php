<?php


namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index()
    {
        $data['dataUser'] = User::all();
        return view('admin.user.index', $data);
    }


    public function create()
    {
        return view('admin.user.create');
    }


    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);


        // Simpan data user
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);


        return redirect()->route('user.index')->with('success', 'Penambahan Data Berhasil!');
    }


    public function edit(string $id)
    {
        $data['dataUser'] = User::findOrFail($id);
        return view('admin.user.edit', $data);
    }


    public function update(Request $request, string $id)
    {
        // 1. Validasi Input (email mengabaikan id user yang sedang di-edit)
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);


        $user = User::findOrFail($id);
        $user->name  = $request->name;
        $user->email = $request->email;


        // Jika password diisi, lakukan enkripsi dan update
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }


        $user->save();


        return redirect()->route('user.index')->with('success', 'Data Berhasil Diperbarui!');
    }


    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();


        return redirect()->route('user.index')->with('success', 'Data Berhasil Dihapus!');
    }
}
