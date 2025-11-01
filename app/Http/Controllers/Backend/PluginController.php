<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;

class PluginController extends Controller
{
    use ImageUpload;

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:plugin-setting');
    }

    public function plugin($type)
    {

        $titles = [
            'system' => __('Third Party System Plugins'),
            'sms' => __('All Plugins adds the ability to send SMS'),
            'notification' => __('Most Popular Push Notification Plugin'),
            'billing_service_provider' => 'Billing Service Provider',
            'virtual_card_provider' => 'Card Provider',
        ];

        $title = $titles[$type];
        $plugins = Plugin::type($type)->get();

        $isLink = false;
        if ($type == 'notification') {
            $isLink = true;
        }

        return view('backend.setting.plugin.index', compact('plugins', 'title', 'isLink'));
    }

    public function pluginData($id)
    {
        $plugin = Plugin::find($id);

        return view('backend.setting.plugin.include.__plugin_data', compact('plugin'))->render();
    }

    public function update(Request $request, $id)
    {
        $plugin = Plugin::find($id);
        $status = (bool) $request->status;

        if ($plugin->type == 'sms' && $status) {
            Plugin::where('type', 'sms')->update([
                'status' => 0,
            ]);
        }

        $pluginOldData = json_decode($plugin->data, true);
        $requestData = $request->data;

        if ($request->hasFile('data.upload_account_json')) {
            $file = $request->file('data.upload_account_json');
            $requestData['upload_account_json'] = self::fileUpload($file, $pluginOldData['upload_account_json'] ?? null);
        }

        $plugin->update([
            'data' => json_encode($requestData),
            'status' => $status,
        ]);

        notify()->success(__('Settings has been saved'));

        return back();
    }
}
