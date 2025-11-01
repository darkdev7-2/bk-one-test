<?php

namespace App\Traits;

use App\Events\NotificationEvent;
use App\Mail\MailSend;
use App\Models\EmailTemplate;
use App\Models\Notification;
use App\Models\PushNotificationTemplate;
use App\Models\SmsTemplate;
use App\Models\UserDevice;
use Exception;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait NotifyTrait
{
    use FcmTrait, SmsTrait;

    // ============================= mail template helper ===================================================
    protected function mailNotify($email, $code, $shortcodes = null)
    {
        try {
            $template = EmailTemplate::where('status', true)->where('code', $code)->first();
            if ($template) {
                $find = array_keys($shortcodes);
                $replace = array_values($shortcodes);
                $details = [
                    'subject' => str_replace($find, $replace, $template->subject),
                    'banner' => asset($template->banner),
                    'title' => str_replace($find, $replace, $template->title),
                    'salutation' => str_replace($find, $replace, $template->salutation),
                    'message_body' => str_replace($find, $replace, $template->message_body),
                    'button_level' => $template->button_level,
                    'button_link' => str_replace($find, $replace, $template->button_link),
                    'footer_status' => $template->footer_status,
                    'footer_body' => str_replace($find, $replace, $template->footer_body),
                    'bottom_status' => $template->bottom_status,
                    'bottom_title' => str_replace($find, $replace, $template->bottom_title),
                    'bottom_body' => str_replace($find, $replace, $template->bottom_body),

                    'site_logo' => asset(setting('site_logo', 'global')),
                    'site_title' => setting('site_title', 'global'),
                    'site_link' => route('home'),
                ];

                if ($code == 'email_verification') {
                    return (new MailMessage)
                        ->subject($details['subject'])
                        ->markdown('backend.mail.user-mail-send', ['details' => $details]);
                }

                return Mail::to($email)->send(new MailSend($details));
            }
        } catch (Exception $e) {
            notify()->error('Mail send failed : '.$e->getMessage(), 'Error');
        }
    }

    // ============================= push notification template helper ===================================================
    protected function pushNotify($code, $shortcodes, $action, $userId, $for = 'User')
    {
        try {
            $template = PushNotificationTemplate::where('status', true)->where('for', ucfirst($for))->where('code', $code)->first();

            if ($template) {
                $find = array_keys($shortcodes);
                $replace = array_values($shortcodes);
                $data = [
                    'icon' => $template->icon,
                    'user_id' => $userId,
                    'for' => Str::snake($template->for),
                    'title' => str_replace($find, $replace, $template->title),
                    'notice' => strip_tags(str_replace($find, $replace, $template->message_body)),
                    'action_url' => $action,
                ];

                Notification::create($data);

                if (plugin_active('Firebase')) {
                    $this->fcmNotify($template, $shortcodes, $action, $userId);
                }

                $pusher_credentials = config('broadcasting.connections.pusher');
                if ($pusher_credentials) {
                    $userId = $template->for == 'Admin' ? '' : $userId;
                    event(new NotificationEvent($template->for, $data, $userId));
                }
            }
        } catch (Exception $e) {
        }
    }

    // ============================= sms notification template helper ===================================================
    protected function smsNotify($code, $shortcodes, $phone)
    {

        if (! config('sms.default') && ! $phone) {
            return false;
        }

        try {
            $template = SmsTemplate::where('status', true)->where('code', $code)->first();
            if ($template) {
                $find = array_keys($shortcodes);
                $replace = array_values($shortcodes);

                $message = [
                    'message_body' => str_replace($find, $replace, $template->message_body),
                ];
                self::sendSms($phone, $message);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // ============================= fcm notification template helper ===================================================
    protected function fcmNotify($template, $shortcodes, $action, $userId)
    {
        try {
            $find = array_keys($shortcodes);
            $replace = array_values($shortcodes);

            $title = str_replace($find, $replace, $template->title);
            $body = strip_tags(str_replace($find, $replace, $template->message_body));

            // Get user device tokens
            $token = UserDevice::where('user_id', $userId)->first()?->fcm_token;

            if ($token == null) {
                return;
            }

            $data = [
                'icon' => $template->icon,
                'user_id' => $userId,
                'for' => strtolower($template->for),
                'title' => $title,
                'notice' => $body,
                'action_url' => $action,
            ];

            $this->sendFcmNotification($token, $title, $body, $data);
        } catch (Exception $e) {
            // Silent fail
        }
    }
}
