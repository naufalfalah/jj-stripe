<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::get('/', function () {
    if (auth('admin')->check()) {
        return redirect()->route('admin.home');
    } else {
        return redirect()->route('user.dashboard');
    }
});

// admin auth routes
Route::namespace('App\Http\Controllers\Auth')->group(function () {
    Route::controller('AdminLoginController')->group(function () {
        Route::middleware('XSS')->prefix('web_admin')->as('admin.')->group(function () {
            Route::get('/login', 'showLoginForm')->name('login');
            Route::post('/logout', 'logout')->name('logout');
            Route::post('/login', 'login')->name('login.submit');

            Route::get('/change_password', 'change_password')->name('change_password');
            Route::get('/forget_password', 'forget_password')->name('forget_password');
            Route::post('/send_email', 'send_email')->name('send_email');
            Route::get('/add_new_pasword/{id}', 'password_screen')->name('add_new_pasword');
            Route::post('/save_new_password', 'save_new_password')->name('save_new_password');
        });
    });

    Route::controller('RegisterController')->group(function () {
        Route::post('check_email', 'check_email')->name('check_email');
    });
});

Route::middleware(['admin'])->group(function () {
    Route::namespace('App\Http\Controllers\Administrator')->group(function () {
        Route::prefix('web_admin')->as('admin.')->group(function () {
            Route::middleware(['XSS'])->group(function () {
                Route::controller('DashboardController')->group(function () {
                    Route::get('/', 'index')->name('home');
                    Route::post('/save_sub_account', 'save_sub_account')->name('save_sub_account');
                    Route::get('/notifications', 'notifications')->name('notifications');
                    Route::get('/update_notifications', 'update_notifications')->name('update_notifications');
                    Route::get('/update-sub-account-status/{id}/{status}', 'update_sub_account_status')->name('update_sub_account_status');
                    Route::get('/sub_account/{id}', 'subAccountShow')->name('sub_account');
                });

                Route::controller('MessageTemplateController')->group(function () {
                    Route::prefix('message_template')->as('message_template.')->group(function () {
                        Route::get('/', 'index')->name('all');
                        Route::post('/wp_message_store', 'wp_message_store')->name('wp_message_store');
                    });
                });

                Route::controller('ClientFilesManagement')->group(function () {
                    Route::prefix('file_manager')->as('file_manager.')->group(function () {
                        Route::get('/files', 'files_view')->name('view');
                        Route::get('/file_detail/{id}', 'file_detail')->name('file_detail');
                        Route::get('/file_list', 'files_list')->name('file_list');
                        Route::get('/delete/{id}', 'delete_file')->name('delete');
                        Route::post('get_client_files', 'get_client_files')->name('get_client_files');
                        Route::post('add_file', 'save_file')->name('save_file');
                    });
                });

                Route::controller('MessageTemplateController')->group(function () {
                    Route::prefix('message-template')->as('message-template.')->group(function () {
                        Route::get('/all', 'index')->name('all');
                        Route::get('/template/{id}/{send_message?}', 'msg_tmp_details')->name('temp_details');
                        Route::post('/save', 'save')->name('save');
                        Route::post('/send', 'send')->name('send');
                        Route::post('/copy_temp', 'copy_temp')->name('copy_temp');
                        Route::get('/delete/{id}', 'delete')->name('delete');
                    });
                });

                Route::controller('DashboardController')->group(function () {
                    Route::prefix('profile')->as('profile.')->group(function () {
                        Route::get('/', 'edit_profile')->name('edit');
                        Route::post('/update', 'update_profile')->name('update');
                        Route::post('/set_token', 'save_device_token')->name('save_device_token');

                    });
                });

                Route::prefix('sub_account/{sub_account_id}')->as('sub_account.')->group(function () {
                    // Client Running Ads
                    Route::controller('RunningAdsController')->group(function () {
                        Route::prefix('advertisements')->as('advertisements.')->group(function () {
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
                    });

                    // Client Management
                    Route::controller('ClientManagmentController')->group(function () {
                        Route::prefix('client-management')->as('client-management.')->group(function () {
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
                    });

                    // Google Ads Report
                    Route::controller('GoogleAdsReportController')->group(function () {
                        Route::prefix('google-ads-report')->as('google-ads-report.')->group(function () {
                            Route::get('/', 'google_ads_report')->name('google_ads_report');
                            Route::get('/download_pdf', 'download_pdf')->name('download_pdf');
                            Route::post('/save_google_report', 'save_google_report')->name('save_google_report');
                            Route::post('/campaign_note_save', 'campaign_note_save')->name('campaign_note_save');
                            Route::get('/campaign_note_delete/{id}', 'campaign_note_delete')->name('campaign_note_delete');
                            Route::get('/google_act_disconnect', 'google_act_disconnect')->name('google_act_disconnect');
                            Route::post('update_act_expiry_date', 'update_act_expiry_date')->name('update_act_expiry_date');
                        });
                    });
                });

                // Settings
                Route::controller('GoogleAccountController')->group(function () {
                    Route::prefix('setting')->as('setting.')->group(function () {
                        // Admin Google Connectivity
                        Route::get('/google_account', 'index')->name('google_account');
                        Route::get('/connect', 'getAuthUrl')->name('connect');
                        Route::get('/oauth', 'oauth')->name('oauth');
                        Route::get('/disconnect', 'disconnect')->name('disconnect');
                        Route::get('/refresh_token/{id}', 'refresh_token')->name('refresh_token');

                        // Taxes & vat charges
                        Route::get('/taxes', 'SettingController@tex_vat_charges')->name('taxes');
                        Route::post('/tax_store', 'SettingController@tax_store')->name('tax_store');

                        // topup setting
                        Route::post('/topup_store', 'SettingController@topup_store')->name('topup_store');

                        // WhatsApp Message Template
                        Route::get('/whatsapp_temp', 'SettingController@whatsapp_temp')->name('whatsapp_temp');
                        Route::post('/wp_message_store', 'SettingController@wp_message_store')->name('wp_message_store');

                        // assign template to client
                        Route::get('/assign_template_to_clients', 'SettingController@assign_template_to_clients')->name('assign_template_to_clients');
                    });
                });

                // Assign Task
                Route::controller('AssignTaskController')->group(function () {
                    Route::prefix('assign_task')->as('assign_task.')->group(function () {
                        Route::get('/create_design', 'create_design')->name('create_design');
                        Route::post('/save_designer', 'save_designer')->name('save_designer');
                        Route::get('/edit/{id}', 'edit')->name('edit');
                        Route::get('/delete/{id}', 'delete')->name('delete');
                        Route::get('/upload_images', 'upload_images')->name('upload_images');
                        Route::post('/save_designer_images', 'saveDesignerImages')->name('save_designer_images');
                        Route::get('/designer_images', 'designer_images')->name('designer_images');
                        Route::get('/designer_image_delete/{id}', 'designer_image_delete')->name('designer_image_delete');
                    });
                });
                // Assign Task

                // ebook start
                Route::controller('EbookController')->group(function () {
                    Route::prefix('ebook')->as('ebook.')->group(function () {
                        Route::get('/add', 'add_ebook')->name('add');
                        Route::get('/all', 'index')->name('all');
                        Route::post('/save', 'save_ebook')->name('save');
                        Route::get('/edit/{id}', 'edit')->name('edit');
                        Route::get('/delete/{id}', 'delete')->name('delete');
                        Route::get('/details/{id}', 'details')->name('details');
                    });
                });
                // ebook end

                Route::controller('AgencyController')->group(function () {
                    Route::prefix('agency')->as('agency.')->group(function () {
                        Route::get('/', 'index')->name('all');
                        Route::post('/save', 'save')->name('save');
                        Route::get('/edit/{id}', 'edit')->name('edit');
                    });
                });

                Route::controller('PackageController')->group(function () {
                    Route::prefix('package')->as('package.')->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::post('/', 'store')->name('store');
                        Route::get('/{id}', 'edit')->name('edit');
                    });
                });

                Route::controller('NotificationController')->group(function () {
                    Route::prefix('notification')->as('notification.')->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/create', 'create')->name('create');
                        Route::post('/', 'store')->name('store');
                        Route::get('/{id}', 'edit')->name('edit');
                        Route::delete('/{id}', 'destroy')->name('destroy');
                        Route::get('/{id}/schedule', 'schedule')->name('schedule');
                        Route::get('/{id}/schedule/create', 'scheduleCreate')->name('schedule-create');
                        Route::post('/{id}/schedule', 'scheduleStore')->name('schedule-store');
                    });
                });

                // Facebook Ads Report
                Route::controller('FacebookAdsReportController')->group(function () {
                    Route::prefix('facebook-ads-report')->as('facebook-ads-report.')->group(function () {
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
                });

                Route::controller('ClientLeadManagmentController')->group(function () {
                    Route::prefix('lead-management')->as('lead-management.')->group(function () {
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
                        Route::post('/send_lead_to_discord', 'send_lead_to_discord')->name('send_lead_to_discord');

                        Route::post('/activity_save', 'activity_save')->name('activity_save');
                        Route::get('/activity_delete/{id}', 'activity_delete')->name('activity_delete');
                        Route::post('/set_follow_up', 'set_follow_up')->name('set_follow_up');
                        Route::get('/unset_follow_up/{id}', 'unset_follow_up')->name('unset_follow_up');

                        Route::get('/get_follow_ups', 'get_follow_ups')->name('get_follow_ups');
                        Route::post('/update_status', 'update_status')->name('update_status');
                    });
                });

                Route::controller('GoogleAccountController')->group(function () {
                    Route::prefix('google')->as('google.')->group(function () {
                        Route::get('/index', 'index')->name('index');
                        Route::get('/oauth', 'oauth')->name('oauth');
                        Route::get('/integrate', 'getAuthUrl')->name('integrate');
                        Route::post('/create-event', 'save_event')->name('save_event');
                        Route::put('/update-event', 'update_event')->name('update_event');
                        Route::delete('/delete-event', 'delete_event')->name('delete_event');
                        Route::get('/get-events', 'get_google_calender_events')->name('get_google_calender_events');
                        Route::get('/google_calender_disconnected', 'google_calender_disconnected')->name('google_calender_disconnected');
                    });
                });

                Route::controller('EmailTemplateController')->group(function () {
                    Route::prefix('email-template')->as('email-template.')->group(function () {
                        Route::get('/all', 'index')->name('all');
                        Route::get('/template/{id}/{send_email?}', 'email_tmp_details')->name('temp_details');
                        Route::post('/save', 'save')->name('save');
                        Route::post('/send', 'send')->name('send');
                        Route::post('/copy_temp', 'copy_temp')->name('copy_temp');
                        Route::get('/delete/{id}', 'delete')->name('delete');
                    });
                });

                // user Management
                Route::middleware(['XSS'])->prefix('user-management')->as('user-management.')->controller('EmployeeManagmentController')->group(function () {
                    Route::get('/add-user', 'add')->name('add-user');
                    Route::post('/save', 'save')->name('save');
                    Route::get('/view', 'view')->name('view');
                    Route::get('/edit/{id}', 'edit')->name('edit');
                    Route::get('/delete/{id}', 'delete')->name('delete');
                    Route::post('/update-password', 'update_password')->name('update-password');
                });
                // user role
                Route::controller('RoleController')->group(function () {
                    Route::prefix('role')->as('role.')->group(function () {
                        Route::get('/all', 'index')->name('all');
                        Route::get('/add', 'add')->name('add');
                        Route::post('/save', 'save')->name('save');
                        Route::get('/edit/{id}', 'edit')->name('edit');
                        Route::get('/delete{id}', 'delete')->name('delete');
                    });
                });

                // permission
                Route::controller('PermissionController')->group(function () {
                    Route::prefix('permission')->as('permission.')->group(function () {
                        Route::get('/', 'index')->name('permission');
                        Route::post('/save', 'save')->name('save');
                        Route::get('/edit/{id}', 'edit')->name('edit');
                        Route::get('/delete{id}', 'delete')->name('delete');
                    });
                });

                // Permission Type
                Route::controller('PermissionTypeController')->group(function () {
                    Route::prefix('permission_type')->as('permission_type.')->group(function () {
                        Route::get('/', 'permissionType')->name('permission_type');
                        Route::post('/save', 'save')->name('save');
                        Route::get('/edit/{id}', 'edit')->name('edit');
                        Route::get('/delete{id}', 'delete')->name('delete');
                    });
                });
            });
        });
    });
});

// web user auth routes start
Route::namespace('App\Http\Controllers\Auth')->group(function () {
    Route::middleware('XSS')->prefix('auth')->as('auth.')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::post('/login/submit', 'login')->name('login.submit');
            Route::post('/logout', 'logout')->name('logout');
        });

        Route::controller('ForgetPasswordController')->group(function () {
            Route::get('/forget_password', 'forget_password')->name('forget_password');
            Route::post('/send_email', 'send_email')->name('send_email');
            Route::get('/add_new_pasword/{id}', 'password_screen')->name('add_new_pasword');
            Route::post('/save_new_password', 'save_new_password')->name('save_new_password');
        });

        Route::controller('RegisterController')->group(function () {
            Route::get('/register', 'showRegistrationForm')->name('register');
            Route::post('/register/submit', 'register')->name('register.submit');
            Route::get('/success_message', 'success_message')->name('success_message');
            Route::get('/verify/{id}', 'password_screen')->name('verify');
            Route::post('/password/submit', 'password_save')->name('password.submit');

            Route::get('/regenerate-sheet', 'regenerateSheet');
        });

        Route::controller(AuthController::class)->group(function () {
            Route::get('/otp/verify/{id}', 'showOtpForm')->name('otp.verify');
            Route::post('/otp/verify', 'verifyOtp')->name('otp.verify.post');
            Route::post('/resend_otp', 'resendOtp')->name('resend_otp');
            Route::get('/resend_email/{id}', 'resendEmail')->name('resend_email');
        });

        Route::controller('SocialiteController')->group(function () {
            Route::get('/social/{provider}', 'redirectToProvider')->name('social');
            Route::get('/social/{provider}/callback', 'handleProviderCallback');
        });
    });
});

Route::namespace('App\Http\Controllers\Frontend')->group(function () {
    Route::prefix('client')->group(function () {
        Route::get('/response/{id}', 'LeadController@view_client_file_activity')->name('client.response');
        Route::get('/page/{id}/{client_id}', 'PageController@client_page_view')->name('client.page_view');
        Route::get('/file/{id}/{client_id}', 'FileManagerController@client_file_view')->name('client.file_view');
        Route::get('/file-view/{id}', 'FileManagerController@file_view')->name('file_view');
    });

    Route::prefix('user')->as('user.')->middleware(['client', 'XSS'])->group(function () {
        Route::controller('DashboardController')->group(function () {
            Route::get('/', 'index')->name('dashboard');
            Route::post('/get_latest_leads_dashboard', 'get_leads')->name('get_latest_leads_dashboard');

            Route::prefix('profile')->as('profile.')->group(function () {
                Route::get('/', 'edit_profile')->name('edit');
                Route::post('/update', 'update_profile')->name('update');
                Route::post('/update_password', 'update_password')->name('update_password');
                Route::post('/set_token', 'save_device_token')->name('save_device_token');
                Route::put('/user_notification', 'updateUserNotification')->name('update_user_notification');
                Route::put('/lead_filter', 'updateClientLeadFilter')->name('update_lead_filter');
            });
        });

        // Google Calender Management
        Route::controller('GoogleAccountController')->group(function () {
            Route::prefix('google')->as('google.')->group(function () {
                Route::get('/index', 'index')->name('index');
                Route::get('/oauth', 'oauth')->name('oauth');
                Route::get('/integrate', 'getAuthUrl')->name('integrate');
                Route::post('/create-event', 'save_event')->name('save_event');
                Route::put('/update-event', 'update_event')->name('update_event');
                Route::delete('/delete-event', 'delete_event')->name('delete_event');
                Route::get('/get-events', 'get_google_calender_events')->name('get_google_calender_events');
                Route::get('/google_calender_disconnected', 'google_calender_disconnected')->name('google_calender_disconnected');
            });
        });

        Route::controller('MessageTemplateController')->group(function () {
            Route::prefix('message-template')->as('message-template.')->group(function () {
                Route::get('/all', 'index')->name('all');
                Route::get('/template/{id}/{send_message?}', 'msg_tmp_details')->name('temp_details');
                Route::post('/save', 'save')->name('save');
                Route::post('/send', 'send')->name('send');
                Route::post('/copy_temp', 'copy_temp')->name('copy_temp');
                Route::get('/delete/{id}', 'delete')->name('delete');
            });
        });

        Route::controller('UserMessageTemplateController')->group(function () {
            Route::prefix('whatsap_message_template')->as('whatsap_message_template.')->group(function () {
                Route::get('/', 'index')->name('all');
                Route::post('/', 'wp_message_update')->name('wp_message_update');
            });
        });

        Route::controller('EmailTemplateController')->group(function () {
            Route::prefix('email-template')->as('email-template.')->group(function () {
                Route::get('/all', 'index')->name('all');
                Route::get('/template/{id}/{send_email?}', 'email_tmp_details')->name('temp_details');
                Route::post('/save', 'save')->name('save');
                Route::post('/send', 'send')->name('send');
                Route::post('/copy_temp', 'copy_temp')->name('copy_temp');
                Route::get('/delete/{id}', 'delete')->name('delete');
            });
        });

        Route::controller('FileManagerController')->group(function () {
            Route::prefix('file_manager')->as('file_manager.')->group(function () {
                Route::get('/files', 'files_view')->name('view');
                Route::get('/file_detail/{id}', 'file_detail')->name('file_detail');
                Route::get('/file_list', 'files_list')->name('file_list');
                Route::get('/delete/{id}', 'delete_file')->name('delete');
                Route::post('/save_folder', 'save_folder')->name('save_folder');
                Route::post('/update_file_name', 'update_file_name')->name('update_file_name');
                Route::post('/save_file', 'save_file')->name('save_file');
                Route::post('get_client_files', 'get_client_files')->name('get_client_files');
                Route::post('/send_file', 'send_file')->name('send_file');
            });
        });

        Route::controller('PageController')->group(function () {
            Route::prefix('page')->as('page.')->group(function () {
                Route::get('/page', 'page_view')->name('view');
                Route::post('/page_save', 'page_save')->name('save');
                Route::get('/page_details/{id}', 'page_details')->name('page_details');
                Route::get('/add_page/{id}', 'add_page')->name('add_page');
                Route::get('/page_preview/{id}', 'page_preview')->name('page_preview');
                Route::get('/delete_page/{id}', 'delete_page')->name('delete_page');
                Route::post('/send', 'send')->name('send');
                Route::get('/edit_page/{id}', 'edit_page')->name('edit_page');
            });
        });

        Route::controller('ClientTourController')->group(function () {
            Route::prefix('client_tour')->as('client_tour.')->group(function () {
                Route::get('/restart', 'restart')->name('restart');
            });
        });

        Route::controller('DashboardController')->group(function () {
            Route::get('/notifications', 'notifications')->name('notifications');
            Route::get('/update_notifications', 'update_notifications')->name('update_notifications');
        });

        // Leads Management
        Route::controller('LeadController')->group(function () {
            Route::prefix('leads-management')->as('leads-management.')->group(function () {
                Route::get('/all', 'index')->name('all');
                Route::get('/client/{id}', 'client_details')->name('client_details');
                Route::post('/save', 'save')->name('save');
                Route::post('/assign_lead', 'assign_lead_to_isa')->name('assign_lead');
                Route::post('/add_lead_to_spam', 'add_lead_to_spam')->name('add_lead_to_spam');
                Route::post('/group_save', 'group_save')->name('group_save');
                Route::post('/group_lead_save', 'group_lead_save')->name('group_lead_save');
                Route::get('/delete_group/{id}', 'delete_group')->name('delete_group');
                Route::post('/import_file', 'import_file')->name('import_file');
                Route::get('/delete/{id}/{is_reload?}', 'delete')->name('delete');
                Route::post('/activity_save', 'activity_save')->name('activity_save');
                Route::get('/activity_delete/{id}', 'activity_delete')->name('activity_delete');
                Route::post('/set_follow_up', 'set_follow_up')->name('set_follow_up');
                Route::get('/unset_follow_up/{id}', 'unset_follow_up')->name('unset_follow_up');
                Route::get('/get_follow_ups', 'get_follow_ups')->name('get_follow_ups');
                Route::post('/update_status', 'update_status')->name('update_status');

                Route::get('/leads', 'ppc_leads')->name('leads');
                Route::post('/lead_status', 'lead_status')->name('lead_status');
                Route::post('/lead_status', 'get_leads')->name('get_leads_all');
            });
        });

        // Client Wallet
        Route::controller('WalletController')->group(function () {
            Route::prefix('wallet')->as('wallet.')->group(function () {
                Route::get('/all', 'index')->name('all');
                Route::get('/add', 'add_top_up')->name('add');
                Route::post('/save', 'save')->name('save');
                Route::get('/transaction_table', 'transaction_table')->name('transaction_table');
                Route::get('transactions', 'transactions')->name('transactions');
                Route::get('sub_wallets', 'sub_wallets')->name('sub_wallets');
                Route::post('sub_wallets_transactions', 'sub_wallets_transactions')->name('sub_wallets_transactions');
                Route::post('add_topup_subwallet', 'add_topup_subwallet')->name('add_topup_subwallet');
                Route::get('transfer-funds', 'transfer_funds')->name('transfer_funds');
                Route::Post('funds_save', 'funds_save')->name('funds_save');
                Route::get('/view_fund_transections', 'view_fund_transections')->name('view_fund_transections');
                Route::get('/transaction_report', 'transaction_report')->name('transaction_report');
                Route::get('/add_paynow_transaction_id', 'add_paynow_transaction_id')->name('add_paynow_transaction_id');
                Route::post('/wallet_close', 'walletClose')->name('wallet_close');
            });
        });

        // Stripe Payment
        Route::controller('StripePaymentController')->group(function () {
            Route::prefix('stripe')->as('stripe.')->group(function () {
                Route::get('/checkout/{price}/{product}/{ad_id}', 'stripeCheckout')->name('checkout');
                Route::get('/checkout/success', 'stripeCheckoutSuccess')->name('checkout.success');
            });
        });

        // PayNow Payment
        Route::controller('PayNowPaymentController')->group(function () {
            Route::prefix('paynow')->as('paynow.')->group(function () {
                Route::get('/checkout', 'payNowCheckout')->name('checkout');
                Route::get('/checkout/success', 'paynowCheckoutSuccess')->name('checkout.success');
            });
        });

        // Client Ads
        Route::controller('AdsManagementController')->group(function () {
            Route::prefix('ads')->as('ads.')->group(function () {
                Route::get('/all', 'index')->name('all');
                Route::get('/add', 'add_ads')->name('add');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::get('/edit/{id}', 'edit_ads')->name('edit');
                Route::put('/update/{id}', 'update')->name('update');
                Route::post('/save', 'save')->name('save');
                Route::get('/view/progress/{id}', 'view_progress')->name('view_progress');
                Route::post('/view/progress/get_leads_data', 'get_leads_data')->name('get_leads_data');
                Route::post('/view/progress/lead_admin_status', 'lead_admin_status')->name('lead_admin_status');
                Route::get('/check_domain', 'check_domain')->name('check_domain');
            });
        });

        // Client Ads Report
        Route::controller('AdsReportController')->group(function () {
            Route::prefix('report')->as('report.')->group(function () {
                Route::get('/', 'index')->name('view');
                Route::get('/slip/{id}', 'slip')->name('slip');
            });
        });

        Route::controller('MessageTemplateController')->group(function () {
            Route::prefix('message_template')->as('message_template.')->group(function () {
                Route::get('/', 'whatsapp_temp')->name('whatsapp_temp');
                Route::post('/wp_message_store', 'wp_message_store')->name('wp_message_store');
            });
        });

        // Google Ads Report
        Route::controller('GoogleAdsReportFrontendController')->group(function () {
            Route::prefix('google-ads-report')->as('google-ads-report.')->group(function () {
                Route::get('/', 'google_ads_report')->name('google_ads_report');
                Route::get('/download_pdf', 'download_pdf')->name('download_pdf');
                Route::post('/save_google_report', 'save_google_report')->name('save_google_report');
                Route::post('/campaign_note_save', 'campaign_note_save')->name('campaign_note_save');
                Route::get('/campaign_note_delete/{id}', 'campaign_note_delete')->name('campaign_note_delete');
                Route::post('update_act_expiry_date', 'update_act_expiry_date')->name('update_act_expiry_date');
            });
        });

        Route::controller('PackageFrontendController')->group(function () {
            Route::prefix('package')->as('package.')->group(function () {
                Route::post('/', 'buy')->name('buy');
            });
        });
    });
});

// open route for file view of ebook
Route::get('/ebook_file_view/{slug}/{id}', [App\Http\Controllers\Administrator\EbookController::class, 'file_view'])->name('ebook_file_view');
// open route for file view of ebook

require __DIR__.'/admin.php';
require __DIR__.'/user.php';
