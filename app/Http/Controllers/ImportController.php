<?php

namespace App\Http\Controllers;

use App\Exports\ImportTemplateExport;
use App\Imports\SpreadsheetImport;
use App\Services\Imports\ImportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ImportController extends Controller
{
    private const TYPES = ['products', 'suppliers', 'customers', 'purchases', 'sales'];

    public function __construct(private readonly ImportService $importService)
    {
    }

    public function index()
    {
        return view('imports.index');
    }

    public function template(string $type)
    {
        $this->guardType($type);
        return Excel::download(new ImportTemplateExport($type), "mahwi-{$type}-import-template.xlsx");
    }

    public function upload(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:' . implode(',', self::TYPES)],
            'file' => ['required', 'file', 'mimes:xlsx,csv,txt', 'max:10240'],
        ]);

        $type = $request->string('type')->toString();
        $this->guardType($type);
        $path = $request->file('file')->store('imports');

        try {
            $sheets = Excel::toArray(new SpreadsheetImport(), Storage::path($path));
            $rows = $sheets[0] ?? [];
            $result = $this->importService->validate($type, $rows, (int) $request->user()->shop_id);
            $token = Str::uuid()->toString();
            session()->put("pending_imports.{$token}", [
                'type' => $type,
                'path' => $path,
                'valid' => $result['valid'],
                'errors' => $result['errors'],
                'total' => $result['total'],
                'filename' => $request->file('file')->getClientOriginalName(),
            ]);
            return redirect()->route('imports.preview', $token);
        } catch (Throwable $e) {
            Storage::delete($path);
            return back()->withInput()->with('error', 'The file could not be read: ' . $e->getMessage());
        }
    }

    public function preview(string $token, Request $request)
    {
        $pending = session("pending_imports.{$token}");
        abort_unless($pending, 404);
        $this->guardType($pending['type']);
        return view('imports.preview', compact('pending', 'token'));
    }

    public function confirm(string $token, Request $request)
    {
        $pending = session("pending_imports.{$token}");
        abort_unless($pending, 404);
        $this->guardType($pending['type']);

        if (!empty($pending['errors']) || empty($pending['valid'])) {
            return redirect()->route('imports.preview', $token)->with('error', 'This import cannot be confirmed until all rows are valid.');
        }

        try {
            $result = $this->importService->import($pending['type'], $pending['valid'], (int) $request->user()->shop_id, (int) $request->user()->id);
            Storage::delete($pending['path']);
            session()->forget("pending_imports.{$token}");
            return redirect()->route('imports.index')->with('success', ucfirst($pending['type']) . ' imported successfully. Created: ' . ($result['created'] ?? 0) . ', Updated: ' . ($result['updated'] ?? 0) . '.');
        } catch (Throwable $e) {
            report($e);
            return redirect()->route('imports.preview', $token)->with('error', 'Import failed and no database changes were kept: ' . $e->getMessage());
        }
    }

    public function cancel(string $token)
    {
        $pending = session("pending_imports.{$token}");
        if ($pending) Storage::delete($pending['path']);
        session()->forget("pending_imports.{$token}");
        return redirect()->route('imports.index')->with('success', 'Import cancelled.');
    }

    private function guardType(string $type): void
    {
        abort_unless(in_array($type, self::TYPES, true), 404);
        $user = request()->user();
        if (in_array($type, ['products', 'suppliers', 'customers'], true)) {
            abort_unless($user->isShopAdmin(), 403);
        } else {
            abort_unless($user->isShopAdmin() || $user->isSeller(), 403);
        }
        abort_unless($user->shop_id, 403, 'Your account is not assigned to a shop.');
    }
}
