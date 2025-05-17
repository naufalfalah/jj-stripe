<?php

use App\Http\Controllers\Administrator\AgencyController;
use App\Http\Controllers\Administrator\AssignTaskController;
use App\Http\Controllers\Administrator\ClientFilesManagement;
use App\Http\Controllers\Administrator\ClientGoogleSheetController;
use App\Http\Controllers\Administrator\ClientLeadManagmentController;
use App\Http\Controllers\Administrator\ClientManagmentController;
use App\Http\Controllers\Administrator\DashboardController;
use App\Http\Controllers\Administrator\EmailTemplateController;
use App\Http\Controllers\Administrator\EmployeeManagmentController;
use App\Http\Controllers\Administrator\FacebookAdsReportController;
use App\Http\Controllers\Administrator\GoogleAccountController;
use App\Http\Controllers\Administrator\GoogleAdsReportController;
use App\Http\Controllers\Administrator\MessageTemplateController;
use App\Http\Controllers\Administrator\NotificationController;
use App\Http\Controllers\Administrator\PackageController;
use App\Http\Controllers\Administrator\PermissionController;
use App\Http\Controllers\Administrator\PermissionTypeController;
use App\Http\Controllers\Administrator\RoleController;
use App\Http\Controllers\Administrator\RunningAdsController;
use App\Http\Controllers\Administrator\ScriptController;
use App\Http\Controllers\Administrator\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['admin', 'XSS'])
    ->prefix('web_admin')
    ->as('admin.')
    ->group(function () {
        Route::controller(DashboardController::class)
            ->group(function () {
                Route::get('/', 'index')->name('home');
                Route::post('/save_sub_account', 'save_sub_account')->name('save_sub_account');
                Route::get('/notifications', 'notifications')->name('notifications');
                Route::get('/update_notifications', 'update_notifications')->name('update_notifications');
                Route::get('/update-sub-account-status/{id}/{status}', 'update_sub_account_status')->name('update_sub_account_status');
                Route::get('/sub_account/{id}', 'subAccountShow')->name('sub_account');

                Route::get('/list-timelog', 'clientTimelog')->name('list.timelog');

                Route::post('/task', 'store_task')->name('store_task');
                Route::get('/task', 'show_task')->name('show_task');
                Route::patch('/task', 'update_task')->name('update_task');
                Route::post('/subtask', 'store_subtask')->name('store_subtask');
                Route::get('/subtask', 'show_subtask')->name('show_subtask');
                Route::patch('/subtask', 'update_subtask')->name('update_subtask');
                Route::delete('/subtask', 'destroy_subtask')->name('destroy_subtask');
                Route::post('/project', 'store_project')->name('store_project');
            });

        Route::controller(DashboardController::class)
            ->prefix('profile')
            ->as('profile.')
            ->group(function () {
                Route::get('/', 'edit_profile')->name('edit');
                Route::post('/update', 'update_profile')->name('update');
                Route::post('/set_token', 'save_device_token')->name('save_device_token');
            });

        // form chat
        Route::prefix('form-chat')
            ->group(function () {
                Route::get('/users/{formTaskId}', 'FormChatController@user')->name('get_user_form.admin');
                Route::get('/user-messages/{formTaskId}/{id}/{userType}', 'ChatController@message')->name('get_form_messages.admin');
                Route::get('/user-message-from/{formTaskId}/{id}/{userType}', 'ChatController@getUserFrom')->name('form_get_message_from.admin');
                Route::post('/user-messages-task', 'FormChatController@send')->name('task_send_message_from.admin');
            });

        Route::controller(MessageTemplateController::class)
            ->prefix('message_template')
            ->as('message_template.')
            ->group(function () {
                Route::get('/', 'index')->name('all');
                Route::post('/wp_message_store', 'wp_message_store')->name('wp_message_store');

                Route::get('/all', 'index')->name('all');
                Route::get('/template/{id}/{send_message?}', 'msg_tmp_details')->name('temp_details');
                Route::post('/save', 'save')->name('save');
                Route::post('/send', 'send')->name('send');
                Route::post('/copy_temp', 'copy_temp')->name('copy_temp');
                Route::get('/delete/{id}', 'delete')->name('delete');
            });

        Route::controller(ClientFilesManagement::class)
            ->prefix('file_manager')
            ->as('file_manager.')
            ->group(function () {
                Route::get('/files', 'files_view')->name('view');
                Route::get('/file_detail/{id}', 'file_detail')->name('file_detail');
                Route::get('/file_list', 'files_list')->name('file_list');
                Route::get('/delete/{id}', 'delete_file')->name('delete');
                Route::post('get_client_files', 'get_client_files')->name('get_client_files');
                Route::post('add_file', 'save_file')->name('save_file');
            });

        Route::prefix('sub_account/{sub_account_id}')
            ->as('sub_account.')
            ->group(function () {
                // Client Running Ads
                Route::controller(RunningAdsController::class)
                    ->prefix('advertisements')
                    ->as('advertisements.')
                    ->group(function () {
                        Route::get('/running_ads', 'index')->name('running_ads');
                        Route::get('/get_topups', 'get_topups')->name('get_topups');
                        Route::get('/get_ads', 'get_ads')->name('get_ads');
                        Route::get('/get_main_wallet', 'get_main_wallet')->name('get_main_wallet');
                        Route::get('/get_low_bls_ads', 'get_low_bls_ads')->name('get_low_bls_ads');
                        Route::post('/get_user_bls', 'get_user_bls')->name('get_user_bls');
                        Route::post('/save', 'event_save')->name('event_save');
                        Route::post('/change-status', 'change_status')->name('change-status');
                        Route::post('/change-ads-status', 'change_ads_status')->name('change-ads-status');
                        Route::post('/change-ads_running-status', 'change_ads_running_status')->name('change-ads_running-status');
                        Route::post('/ads-remaining-balance-refund', 'ads_remaining_balance_refund')->name('ads-remaining-balance-refund');
                        Route::post('/edit_add', 'edit_add')->name('edit_add');
                        Route::get('/all-clients', 'transactions')->name('transactions');
                        Route::get('/get_lead', 'get_leads')->name('get_lead');
                        Route::get('/get_follow_ups', 'get_follow_ups')->name('get_follow_ups');
                        Route::get('/lead_detail/{id}', 'lead_detail')->name('lead_detail');
                        Route::post('/lead_status', 'lead_status')->name('lead_status');
                        Route::post('/lead_admin_status', 'lead_admin_status')->name('lead_admin_status');
                        Route::post('/get_all_leads', 'get_all_leads')->name('get_all_leads');
                        Route::post('/get_ppc_leads', 'get_ppc_leads')->name('get_ppc_leads');
                        Route::get('/get_daily_ads_spent', 'get_daily_ads_spent')->name('get_daily_ads_spent');
                        Route::post('/daily_ads_spent_save', 'daily_ads_spent_save')->name('daily_ads_spent_save');
                        Route::get('/get_monthly_ads_spent', 'get_monthly_ads_spent')->name('get_monthly_ads_spent');
                        Route::get('/get_monthly_client', 'get_monthly_client')->name('get_monthly_client');
                        Route::get('/view_progress/{ads_id}/{client_id}', 'view_progress')->name('view_progress');
                        Route::post('/progress/get_leads_data', 'get_leads_data')->name('get_leads_data');
                        Route::post('/lead_admin_status', 'lead_admin_status')->name('lead_admin_status');
                        Route::get('/get_sub_wallet_transactions/{client_id}/{ads_id}', 'get_sub_wallet_transactions')->name('get_sub_wallet_transactions');
                        Route::post('/sub_wallets_transactions', 'sub_wallets_transactions')->name('sub_wallets_transactions');
                    });

                Route::controller(ClientManagmentController::class)
                    ->prefix('client-management')
                    ->as('client-management.')
                    ->group(function () {
                        Route::get('/', 'index')->name('all');
                        Route::get('all_clients/', 'all_clients')->name('all_clients');
                        Route::get('/clone_client/{id}', 'clone_client')->name('clone_client');
                        Route::get('/add', 'add')->name('add');
                        Route::post('/save', 'save')->name('save');
                        Route::post('/get_agency_address', 'get_agency_address')->name('get_agency_address');
                        Route::get('/edit', 'edit')->name('edit');
                        Route::get('/delete/{id}', 'delete')->name('delete');
                        Route::post('/update-password', 'update_password')->name('update-password');

                        // Top Up
                        Route::get('/top_up', 'top_up')->name('top_up');
                        Route::post('/topup_save', 'topup_save')->name('topup_save');
                        Route::get('/topup_edit/{id}', 'topup_edit')->name('topup_edit');
                        Route::get('/topup_delete{id}', 'topup_delete')->name('topup_delete');

                        // Ads Request route
                        Route::get('/ads', 'all_ads')->name('all_ads');
                        Route::get('/ads/create', 'ads_create')->name('ads_create');
                        Route::post('/ads_save', 'ads_save')->name('ads_save');
                        Route::get('/ads_edit/{id}', 'ads_edit')->name('ads_edit');
                        // Route::post('/get_adds', 'get_adds')->name('get_adds');
                        Route::get('/ads_delete/{id}', 'ads_delete')->name('ads_delete');

                        // Google Ads
                        Route::get('/google_ads_campaign', 'google_ads_campaign')->name('google_ads_campaign');
                        Route::get('/google_ads_campaign/edit', 'google_ads_campaign_edit')->name('google_ads_campaign.edit');
                        Route::put('/google_ads_campaign', 'google_ads_campaign_update')->name('google_ads_campaign.update');

                        Route::get('/google_ads_ad_group', 'google_ads_ad_group')->name('google_ads_ad_group');
                        Route::get('/google_ads_ad_group/show', 'google_ads_ad_group_show')->name('google_ads_ad_group.show');

                        Route::get('/google_ads_ad_group_ad', 'google_ads_ad_group_ad')->name('google_ads_ad_group_ad');
                        Route::get('/google_ads_ad_group_ad/create', 'google_ads_create')->name('google_ads.create');
                        Route::post('/google_ads_ad_group_ad', 'google_ads_store')->name('google_ads.store');
                        Route::post('/google_ads_ad_group_ad/sync', 'google_ads_sync')->name('google_ads.sync');

                        Route::get('/google_ads_conversion_action', 'google_ads_conversion_action')->name('google_ads_conversion_action');
                        Route::get('/google_ads_conversion_action/create', 'google_ads_conversion_action_create')->name('google_ads_conversion_action.create');
                        Route::post('/google_ads_conversion_action', 'google_ads_conversion_action_store')->name('google_ads_conversion_action.store');
                    });

                Route::controller(GoogleAdsReportController::class)
                    ->prefix('google-ads-report')
                    ->as('google-ads-report.')
                    ->group(function () {
                        Route::get('/', 'google_ads_report')->name('google_ads_report');
                        Route::get('/download_pdf', 'download_pdf')->name('download_pdf');
                        Route::post('/save_google_report', 'save_google_report')->name('save_google_report');
                        Route::post('/campaign_note_save', 'campaign_note_save')->name('campaign_note_save');
                        Route::get('/campaign_note_delete/{id}', 'campaign_note_delete')->name('campaign_note_delete');
                        Route::get('/google_act_disconnect', 'google_act_disconnect')->name('google_act_disconnect');
                        Route::post('update_act_expiry_date', 'update_act_expiry_date')->name('update_act_expiry_date');
                    });
            });

        Route::controller(ClientManagmentController::class)
            ->prefix('client-management')
            ->as('client-management.')
            ->group(function () {
                Route::get('/all-clients', 'all_clients')->name('all-clients');
                Route::get('/all-clients', 'all_clients')->name('all_clients');
                Route::get('/view/{id}', 'view')->name('view');
                Route::get('/view/{id}/leads/export', 'client_leads_export')->name('client_leads_export');
                Route::post('/view/{id}/leads/import', 'client_leads_import')->name('client_leads_import');
                Route::post('get_client_files', 'get_client_files')->name('get_client_files');
                Route::get('/get_lead', 'get_leads')->name('get_lead');
                Route::get('/get_follow_ups', 'get_follow_ups')->name('get_follow_ups');
                Route::get('/get_events', 'get_events')->name('get_events');
                Route::get('/get_topups', 'get_topups')->name('get_topups');
                Route::get('/get_ads', 'get_ads')->name('get_ads');
                Route::post('/save', 'event_save')->name('event_save');
                Route::post('/change-status', 'change_status')->name('change-status');
                Route::post('/change-ads-status', 'change_ads_status')->name('change-ads-status');
                Route::get('transactions', 'transactions')->name('transactions');
            });

        Route::prefix('setting')
            ->as('setting.')
            ->group(function () {
                Route::controller(GoogleAccountController::class)
                    ->group(function () {
                        // Admin Google Connectivity
                        Route::get('/google_account', 'index')->name('google_account');
                        Route::get('/connect', 'getAuthUrl')->name('connect');
                        Route::get('/oauth', 'oauth')->name('oauth');
                        Route::get('/disconnect', 'disconnect')->name('disconnect');
                        Route::get('/refresh_token/{id}', 'refresh_token')->name('refresh_token');
                    });

                Route::controller(SettingController::class)
                    ->group(function () {
                        // Taxes & vat charges
                        Route::get('/taxes', 'tex_vat_charges')->name('taxes');
                        Route::post('/tax_store', 'tax_store')->name('tax_store');

                        // topup setting
                        Route::post('/topup_store', 'topup_store')->name('topup_store');

                        // WhatsApp Message Template
                        Route::get('/whatsapp_temp', 'whatsapp_temp')->name('whatsapp_temp');
                        Route::post('/wp_message_store', 'wp_message_store')->name('wp_message_store');

                        // assign template to client
                        Route::get('/assign_template_to_clients', 'assign_template_to_clients')->name('assign_template_to_clients');
                    });
            });

        // Assign Task
        Route::controller(AssignTaskController::class)
            ->prefix('assign_task')
            ->as('assign_task.')
            ->group(function () {
                Route::get('/create_design', 'create_design')->name('create_design');
                Route::post('/save_designer', 'save_designer')->name('save_designer');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::get('/upload_images', 'upload_images')->name('upload_images');
                Route::post('/save_designer_images', 'saveDesignerImages')->name('save_designer_images');
                Route::get('/designer_images', 'designer_images')->name('designer_images');
                Route::get('/designer_image_delete/{id}', 'designer_image_delete')->name('designer_image_delete');
            });
        // Assign Task

        // ebook start
        Route::controller('EbookController')
            ->prefix('ebook')
            ->as('ebook.')
            ->group(function () {
                Route::get('/add', 'add_ebook')->name('add');
                Route::get('/all', 'index')->name('all');
                Route::post('/save', 'save_ebook')->name('save');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::get('/details/{id}', 'details')->name('details');
            });
        // ebook end

        Route::controller(AgencyController::class)
            ->prefix('agency')
            ->as('agency.')
            ->group(function () {
                Route::get('/', 'index')->name('all');
                Route::post('/save', 'save')->name('save');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

        Route::controller(PackageController::class)
            ->prefix('package')
            ->as('package.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'edit')->name('edit');
            });

        Route::controller(NotificationController::class)
            ->prefix('notification')
            ->as('notification.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'edit')->name('edit');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::get('/{id}/schedule', 'schedule')->name('schedule');
                Route::get('/{id}/schedule/create', 'scheduleCreate')->name('schedule-create');
                Route::post('/{id}/schedule', 'scheduleStore')->name('schedule-store');
            });

        Route::controller(FacebookAdsReportController::class)
            ->prefix('facebook-ads-report')
            ->as('facebook-ads-report.')
            ->group(function () {
                Route::get('/', 'fb_ads_report')->name('fb_ads_report');
                Route::get('/download_pdf', 'download_pdf')->name('download_pdf');
                Route::get('/refresh_data', 'refresh_campaign_data')->name('refresh_data');
                Route::post('get_campaigns', 'get_campaigns')->name('get_campaigns');
                Route::post('campaign_note_save', 'campaign_note_save')->name('campaign_note_save');
                Route::get('/campaign_note_delete/{id}', 'campaign_note_delete')->name('campaign_note_delete');
                Route::post('date_range', 'date_range')->name('date_range');
                Route::post('get_ads', 'get_ads')->name('get_ads');
                Route::post('get_adsets', 'get_adsets')->name('get_adsets');
                Route::post('get_country', 'get_country')->name('get_country');
                Route::post('update_act_expiry_date', 'update_act_expiry_date')->name('update_act_expiry_date');
                Route::get('/fb_connect', 'redirectToFacebook')->name('fb_connect');
                Route::get('/fb_oauth', 'oauth')->name('fb_oauth');
                Route::get('/fb_disconnect', 'fb_disconnect')->name('fb_disconnect');
                Route::post('/save_fb_report', 'save_fb_report')->name('save_fb_report');
            });

        Route::controller(ClientLeadManagmentController::class)
            ->prefix('lead-management')
            ->as('lead-management.')
            ->group(function () {
                Route::get('client_all_leads', 'client_all_leads')->name('client_all_leads');
                Route::get('client_leads', 'client_leads')->name('client_leads');
                Route::get('/all', 'client_leads')->name('all');
                Route::post('/assign_lead', 'assign_lead_to_isa')->name('assign_lead');
                Route::get('/client/{id}', 'client_details')->name('client_details');
                Route::post('/save', 'save')->name('save');
                Route::post('/group_save', 'group_save')->name('group_save');
                Route::post('/group_lead_save', 'group_lead_save')->name('group_lead_save');
                Route::get('/delete_group/{id}', 'delete_group')->name('delete_group');
                Route::post('/import_file', 'import_file')->name('import_file');
                Route::get('/delete/{id}/{is_reload?}', 'delete')->name('delete');
                Route::post('/add_lead_to_spam', 'add_lead_to_spam')->name('add_lead_to_spam');

                Route::post('/activity_save', 'activity_save')->name('activity_save');
                Route::get('/activity_delete/{id}', 'activity_delete')->name('activity_delete');
                Route::post('/set_follow_up', 'set_follow_up')->name('set_follow_up');
                Route::get('/unset_follow_up/{id}', 'unset_follow_up')->name('unset_follow_up');

                Route::get('/get_follow_ups', 'get_follow_ups')->name('get_follow_ups');
                Route::post('/update_status', 'update_status')->name('update_status');
            });

        Route::controller(GoogleAccountController::class)
            ->prefix('google')
            ->as('google.')
            ->group(function () {
                Route::get('/index', 'index')->name('index');
                Route::get('/oauth', 'oauth')->name('oauth');
                Route::get('/integrate', 'getAuthUrl')->name('integrate');
                Route::post('/create-event', 'save_event')->name('save_event');
                Route::put('/update-event', 'update_event')->name('update_event');
                Route::delete('/delete-event', 'delete_event')->name('delete_event');
                Route::get('/get-events', 'get_google_calender_events')->name('get_google_calender_events');
                Route::get('/google_calender_disconnected', 'google_calender_disconnected')->name('google_calender_disconnected');
            });

        Route::controller(EmailTemplateController::class)
            ->prefix('email-template')
            ->as('email-template.')
            ->group(function () {
                Route::get('/all', 'index')->name('all');
                Route::get('/template/{id}/{send_email?}', 'email_tmp_details')->name('temp_details');
                Route::post('/save', 'save')->name('save');
                Route::post('/send', 'send')->name('send');
                Route::post('/copy_temp', 'copy_temp')->name('copy_temp');
                Route::get('/delete/{id}', 'delete')->name('delete');
            });

        // user Management
        Route::controller(EmployeeManagmentController::class)
            ->prefix('user-management')
            ->as('user-management.')
            ->group(function () {
                Route::get('/add-user', 'add')->name('add-user');
                Route::post('/save', 'save')->name('save');
                Route::get('/view', 'view')->name('view');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::post('/update-password', 'update_password')->name('update-password');
            });

        // user role
        Route::controller(RoleController::class)
            ->prefix('role')
            ->as('role.')
            ->group(function () {
                Route::get('/all', 'index')->name('all');
                Route::get('/add', 'add')->name('add');
                Route::post('/save', 'save')->name('save');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::get('/delete{id}', 'delete')->name('delete');
            });

        // permission
        Route::controller(PermissionController::class)
            ->prefix('permission')
            ->as('permission.')
            ->group(function () {
                Route::get('/', 'index')->name('permission');
                Route::post('/save', 'save')->name('save');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::get('/delete{id}', 'delete')->name('delete');
            });

        // Permission Type
        Route::controller(PermissionTypeController::class)
            ->prefix('permission_type')
            ->as('permission_type.')
            ->group(function () {
                Route::get('/', 'permissionType')->name('permission_type');
                Route::post('/save', 'save')->name('save');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::get('/delete{id}', 'delete')->name('delete');
            });

        Route::controller(ClientGoogleSheetController::class)
            ->prefix('client_sheets')
            ->as('client_sheets.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
            });

        Route::controller(ScriptController::class)
            ->prefix('scripts')
            ->as('scripts.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/{id}', 'update')->name('update');
                Route::get('/{id}', 'show')->name('show');
                Route::delete('/{id}', 'destroy')->name('delete');
            });
    });
