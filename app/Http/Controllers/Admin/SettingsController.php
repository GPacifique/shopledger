<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use App\Models\SettingAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show the settings form. Stored values win; anything missing falls back to the defaults.
     */
    public function index(Request $request): View
    {
        Gate::authorize('manage-settings');

        $settings = array_merge(Setting::DEFAULTS, Setting::cached());

        return view('admin.settings', compact('settings'));
    }

    /**
     * Save settings. Authorization and validation happen in UpdateSettingsRequest.
     * Only changed keys are written, each with an audit row, in one transaction.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $new     = $request->payload();
        $current = array_merge(Setting::DEFAULTS, Setting::cached());
        $changed = [];

        DB::transaction(function () use ($new, $current, $request, &$changed) {
            foreach ($new as $key => $value) {
                $old = $current[$key] ?? null;

                if ((string) $old === (string) $value) {
                    continue;
                }

                Setting::updateOrCreate(['key' => $key], ['value' => (string) $value]);

                SettingAudit::create([
                    'user_id'    => $request->user()->id,
                    'key'        => $key,
                    'old_value'  => $old === null ? null : (string) $old,
                    'new_value'  => (string) $value,
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                ]);

                $changed[] = $key;
            }
        });

        Setting::flush();

        return back()->with(
            'success',
            $changed
                ? __('Settings saved.')
                : __('No changes to save.')
        );
    }
}