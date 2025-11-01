<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ImageUpload
{
    public function imageUploadTrait($query, $old = null): string
    {
        if (config('app.demo')) {
            return '';
        }

        $allowExt = ['jpeg', 'png', 'jpg', 'gif', 'svg'];
        $ext = strtolower($query->getClientOriginalExtension());

        abort_if($query->getSize() > 5100000, 403, __('Max file size:5MB '));
        abort_if(! in_array($ext, $allowExt), 403, __('Only allow : jpeg, png, jpg, gif, svg'));

        if ($old !== null) {
            self::delete($old);
        }

        $image_name = Str::random(20);
        $image_full_name = $image_name.'.'.$ext;
        $upload_path = 'assets/global/images/';
        $image_url = $upload_path.$image_full_name;
        $success = $query->move($upload_path, $image_full_name);

        return str_replace('assets/', '', $image_url);
    }

    public function fileUpload($query, $old = null)
    {
        if (config('app.demo')) {
            return '';
        }
        
        $file = $query;
        $file_name = $file->getClientOriginalName();
        $file->move('assets/global/files/', $file_name);

        if ($old !== null) {
            self::delete($old);
        }

        return str_replace('assets/', '', 'assets/global/files/'.$file_name);
    }

    protected function delete($path)
    {
        if (file_exists('assets/'.$path)) {
            @unlink('assets/'.$path);
        }
    }
}
