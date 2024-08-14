<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'audit_log_show',
            ],
            [
                'id'    => 18,
                'title' => 'audit_log_access',
            ],
            [
                'id'    => 19,
                'title' => 'user_alert_create',
            ],
            [
                'id'    => 20,
                'title' => 'user_alert_show',
            ],
            [
                'id'    => 21,
                'title' => 'user_alert_delete',
            ],
            [
                'id'    => 22,
                'title' => 'user_alert_access',
            ],
            [
                'id'    => 23,
                'title' => 'faq_management_access',
            ],
            [
                'id'    => 24,
                'title' => 'faq_category_create',
            ],
            [
                'id'    => 25,
                'title' => 'faq_category_edit',
            ],
            [
                'id'    => 26,
                'title' => 'faq_category_show',
            ],
            [
                'id'    => 27,
                'title' => 'faq_category_delete',
            ],
            [
                'id'    => 28,
                'title' => 'faq_category_access',
            ],
            [
                'id'    => 29,
                'title' => 'faq_question_create',
            ],
            [
                'id'    => 30,
                'title' => 'faq_question_edit',
            ],
            [
                'id'    => 31,
                'title' => 'faq_question_show',
            ],
            [
                'id'    => 32,
                'title' => 'faq_question_delete',
            ],
            [
                'id'    => 33,
                'title' => 'faq_question_access',
            ],
            [
                'id'    => 34,
                'title' => 'master_data_access',
            ],
            [
                'id'    => 35,
                'title' => 'operations_access',
            ],
            [
                'id'    => 36,
                'title' => 'finance_access',
            ],
            [
                'id'    => 37,
                'title' => 'hr_management_access',
            ],
            [
                'id'    => 38,
                'title' => 'mobile_app_access',
            ],
            [
                'id'    => 39,
                'title' => 'sale_access',
            ],
            [
                'id'    => 40,
                'title' => 'action_create',
            ],
            [
                'id'    => 41,
                'title' => 'action_edit',
            ],
            [
                'id'    => 42,
                'title' => 'action_show',
            ],
            [
                'id'    => 43,
                'title' => 'action_delete',
            ],
            [
                'id'    => 44,
                'title' => 'action_access',
            ],
            [
                'id'    => 45,
                'title' => 'status_create',
            ],
            [
                'id'    => 46,
                'title' => 'status_edit',
            ],
            [
                'id'    => 47,
                'title' => 'status_show',
            ],
            [
                'id'    => 48,
                'title' => 'status_delete',
            ],
            [
                'id'    => 49,
                'title' => 'status_access',
            ],
            [
                'id'    => 50,
                'title' => 'source_create',
            ],
            [
                'id'    => 51,
                'title' => 'source_edit',
            ],
            [
                'id'    => 52,
                'title' => 'source_show',
            ],
            [
                'id'    => 53,
                'title' => 'source_delete',
            ],
            [
                'id'    => 54,
                'title' => 'source_access',
            ],
            [
                'id'    => 55,
                'title' => 'address_create',
            ],
            [
                'id'    => 56,
                'title' => 'address_edit',
            ],
            [
                'id'    => 57,
                'title' => 'address_show',
            ],
            [
                'id'    => 58,
                'title' => 'address_delete',
            ],
            [
                'id'    => 59,
                'title' => 'address_access',
            ],
            [
                'id'    => 60,
                'title' => 'expenses_category_create',
            ],
            [
                'id'    => 61,
                'title' => 'expenses_category_edit',
            ],
            [
                'id'    => 62,
                'title' => 'expenses_category_show',
            ],
            [
                'id'    => 63,
                'title' => 'expenses_category_delete',
            ],
            [
                'id'    => 64,
                'title' => 'expenses_category_access',
            ],
            [
                'id'    => 65,
                'title' => 'service_type_create',
            ],
            [
                'id'    => 66,
                'title' => 'service_type_edit',
            ],
            [
                'id'    => 67,
                'title' => 'service_type_show',
            ],
            [
                'id'    => 68,
                'title' => 'service_type_delete',
            ],
            [
                'id'    => 69,
                'title' => 'service_type_access',
            ],
            [
                'id'    => 70,
                'title' => 'service_create',
            ],
            [
                'id'    => 71,
                'title' => 'service_edit',
            ],
            [
                'id'    => 72,
                'title' => 'service_show',
            ],
            [
                'id'    => 73,
                'title' => 'service_delete',
            ],
            [
                'id'    => 74,
                'title' => 'service_access',
            ],
            [
                'id'    => 75,
                'title' => 'pricelist_create',
            ],
            [
                'id'    => 76,
                'title' => 'pricelist_edit',
            ],
            [
                'id'    => 77,
                'title' => 'pricelist_show',
            ],
            [
                'id'    => 78,
                'title' => 'pricelist_delete',
            ],
            [
                'id'    => 79,
                'title' => 'pricelist_access',
            ],
            [
                'id'    => 80,
                'title' => 'asset_type_create',
            ],
            [
                'id'    => 81,
                'title' => 'asset_type_edit',
            ],
            [
                'id'    => 82,
                'title' => 'asset_type_show',
            ],
            [
                'id'    => 83,
                'title' => 'asset_type_delete',
            ],
            [
                'id'    => 84,
                'title' => 'asset_type_access',
            ],
            [
                'id'    => 85,
                'title' => 'lead_create',
            ],
            [
                'id'    => 86,
                'title' => 'lead_edit',
            ],
            [
                'id'    => 87,
                'title' => 'lead_show',
            ],
            [
                'id'    => 88,
                'title' => 'lead_delete',
            ],
            [
                'id'    => 89,
                'title' => 'lead_access',
            ],
            [
                'id'    => 90,
                'title' => 'membership_create',
            ],
            [
                'id'    => 91,
                'title' => 'membership_edit',
            ],
            [
                'id'    => 92,
                'title' => 'membership_show',
            ],
            [
                'id'    => 93,
                'title' => 'membership_delete',
            ],
            [
                'id'    => 94,
                'title' => 'membership_access',
            ],
            [
                'id'    => 95,
                'title' => 'locker_create',
            ],
            [
                'id'    => 96,
                'title' => 'locker_edit',
            ],
            [
                'id'    => 97,
                'title' => 'locker_show',
            ],
            [
                'id'    => 98,
                'title' => 'locker_delete',
            ],
            [
                'id'    => 99,
                'title' => 'locker_access',
            ],
            [
                'id'    => 100,
                'title' => 'membership_attendance_create',
            ],
            [
                'id'    => 101,
                'title' => 'membership_attendance_edit',
            ],
            [
                'id'    => 102,
                'title' => 'membership_attendance_show',
            ],
            [
                'id'    => 103,
                'title' => 'membership_attendance_delete',
            ],
            [
                'id'    => 104,
                'title' => 'membership_attendance_access',
            ],
            [
                'id'    => 105,
                'title' => 'expense_create',
            ],
            [
                'id'    => 106,
                'title' => 'expense_edit',
            ],
            [
                'id'    => 107,
                'title' => 'expense_show',
            ],
            [
                'id'    => 108,
                'title' => 'expense_delete',
            ],
            [
                'id'    => 109,
                'title' => 'expense_access',
            ],
            [
                'id'    => 110,
                'title' => 'invoice_create',
            ],
            [
                'id'    => 111,
                'title' => 'invoice_edit',
            ],
            [
                'id'    => 112,
                'title' => 'invoice_show',
            ],
            [
                'id'    => 113,
                'title' => 'invoice_delete',
            ],
            [
                'id'    => 114,
                'title' => 'invoice_access',
            ],
            [
                'id'    => 115,
                'title' => 'payment_create',
            ],
            [
                'id'    => 116,
                'title' => 'payment_edit',
            ],
            [
                'id'    => 117,
                'title' => 'payment_show',
            ],
            [
                'id'    => 118,
                'title' => 'payment_delete',
            ],
            [
                'id'    => 119,
                'title' => 'payment_access',
            ],
            [
                'id'    => 120,
                'title' => 'employee_create',
            ],
            [
                'id'    => 121,
                'title' => 'employee_edit',
            ],
            [
                'id'    => 122,
                'title' => 'employee_show',
            ],
            [
                'id'    => 123,
                'title' => 'employee_delete',
            ],
            [
                'id'    => 124,
                'title' => 'employee_access',
            ],
            [
                'id'    => 125,
                'title' => 'bonu_create',
            ],
            [
                'id'    => 126,
                'title' => 'bonu_edit',
            ],
            [
                'id'    => 127,
                'title' => 'bonu_show',
            ],
            [
                'id'    => 128,
                'title' => 'bonu_delete',
            ],
            [
                'id'    => 129,
                'title' => 'bonu_access',
            ],
            [
                'id'    => 130,
                'title' => 'deduction_create',
            ],
            [
                'id'    => 131,
                'title' => 'deduction_edit',
            ],
            [
                'id'    => 132,
                'title' => 'deduction_show',
            ],
            [
                'id'    => 133,
                'title' => 'deduction_delete',
            ],
            [
                'id'    => 134,
                'title' => 'deduction_access',
            ],
            [
                'id'    => 135,
                'title' => 'loan_create',
            ],
            [
                'id'    => 136,
                'title' => 'loan_edit',
            ],
            [
                'id'    => 137,
                'title' => 'loan_show',
            ],
            [
                'id'    => 138,
                'title' => 'loan_delete',
            ],
            [
                'id'    => 139,
                'title' => 'loan_access',
            ],
            [
                'id'    => 140,
                'title' => 'vacation_create',
            ],
            [
                'id'    => 141,
                'title' => 'vacation_edit',
            ],
            [
                'id'    => 142,
                'title' => 'vacation_show',
            ],
            [
                'id'    => 143,
                'title' => 'vacation_delete',
            ],
            [
                'id'    => 144,
                'title' => 'vacation_access',
            ],
            [
                'id'    => 145,
                'title' => 'document_create',
            ],
            [
                'id'    => 146,
                'title' => 'document_edit',
            ],
            [
                'id'    => 147,
                'title' => 'document_show',
            ],
            [
                'id'    => 148,
                'title' => 'document_delete',
            ],
            [
                'id'    => 149,
                'title' => 'document_access',
            ],
            [
                'id'    => 150,
                'title' => 'employee_setting_create',
            ],
            [
                'id'    => 151,
                'title' => 'employee_setting_edit',
            ],
            [
                'id'    => 152,
                'title' => 'employee_setting_show',
            ],
            [
                'id'    => 153,
                'title' => 'employee_setting_delete',
            ],
            [
                'id'    => 154,
                'title' => 'employee_setting_access',
            ],
            [
                'id'    => 155,
                'title' => 'hotdeal_create',
            ],
            [
                'id'    => 156,
                'title' => 'hotdeal_edit',
            ],
            [
                'id'    => 157,
                'title' => 'hotdeal_show',
            ],
            [
                'id'    => 158,
                'title' => 'hotdeal_delete',
            ],
            [
                'id'    => 159,
                'title' => 'hotdeal_access',
            ],
            [
                'id'    => 160,
                'title' => 'gallery_section_create',
            ],
            [
                'id'    => 161,
                'title' => 'gallery_section_edit',
            ],
            [
                'id'    => 162,
                'title' => 'gallery_section_show',
            ],
            [
                'id'    => 163,
                'title' => 'gallery_section_delete',
            ],
            [
                'id'    => 164,
                'title' => 'gallery_section_access',
            ],
            [
                'id'    => 165,
                'title' => 'gallery_create',
            ],
            [
                'id'    => 166,
                'title' => 'gallery_edit',
            ],
            [
                'id'    => 167,
                'title' => 'gallery_show',
            ],
            [
                'id'    => 168,
                'title' => 'gallery_delete',
            ],
            [
                'id'    => 169,
                'title' => 'gallery_access',
            ],
            [
                'id'    => 170,
                'title' => 'video_section_create',
            ],
            [
                'id'    => 171,
                'title' => 'video_section_edit',
            ],
            [
                'id'    => 172,
                'title' => 'video_section_show',
            ],
            [
                'id'    => 173,
                'title' => 'video_section_delete',
            ],
            [
                'id'    => 174,
                'title' => 'video_section_access',
            ],
            [
                'id'    => 175,
                'title' => 'sales_plan_create',
            ],
            [
                'id'    => 176,
                'title' => 'sales_plan_edit',
            ],
            [
                'id'    => 177,
                'title' => 'sales_plan_show',
            ],
            [
                'id'    => 178,
                'title' => 'sales_plan_delete',
            ],
            [
                'id'    => 179,
                'title' => 'sales_plan_access',
            ],
            [
                'id'    => 180,
                'title' => 'video_create',
            ],
            [
                'id'    => 181,
                'title' => 'video_edit',
            ],
            [
                'id'    => 182,
                'title' => 'video_show',
            ],
            [
                'id'    => 183,
                'title' => 'video_delete',
            ],
            [
                'id'    => 184,
                'title' => 'video_access',
            ],
            [
                'id'    => 185,
                'title' => 'sales_tier_create',
            ],
            [
                'id'    => 186,
                'title' => 'sales_tier_edit',
            ],
            [
                'id'    => 187,
                'title' => 'sales_tier_show',
            ],
            [
                'id'    => 188,
                'title' => 'sales_tier_delete',
            ],
            [
                'id'    => 189,
                'title' => 'sales_tier_access',
            ],
            [
                'id'    => 190,
                'title' => 'newssection_create',
            ],
            [
                'id'    => 191,
                'title' => 'newssection_edit',
            ],
            [
                'id'    => 192,
                'title' => 'newssection_show',
            ],
            [
                'id'    => 193,
                'title' => 'newssection_delete',
            ],
            [
                'id'    => 194,
                'title' => 'newssection_access',
            ],
            [
                'id'    => 195,
                'title' => 'news_create',
            ],
            [
                'id'    => 196,
                'title' => 'news_edit',
            ],
            [
                'id'    => 197,
                'title' => 'news_show',
            ],
            [
                'id'    => 198,
                'title' => 'news_delete',
            ],
            [
                'id'    => 199,
                'title' => 'news_access',
            ],
            [
                'id'    => 200,
                'title' => 'sales_tiers_plan_create',
            ],
            [
                'id'    => 201,
                'title' => 'sales_tiers_plan_edit',
            ],
            [
                'id'    => 202,
                'title' => 'sales_tiers_plan_show',
            ],
            [
                'id'    => 203,
                'title' => 'sales_tiers_plan_delete',
            ],
            [
                'id'    => 204,
                'title' => 'sales_tiers_plan_access',
            ],
            [
                'id'    => 205,
                'title' => 'sales_intensive_create',
            ],
            [
                'id'    => 206,
                'title' => 'sales_intensive_edit',
            ],
            [
                'id'    => 207,
                'title' => 'sales_intensive_show',
            ],
            [
                'id'    => 208,
                'title' => 'sales_intensive_delete',
            ],
            [
                'id'    => 209,
                'title' => 'sales_intensive_access',
            ],
            [
                'id'    => 210,
                'title' => 'asset_create',
            ],
            [
                'id'    => 211,
                'title' => 'asset_edit',
            ],
            [
                'id'    => 212,
                'title' => 'asset_show',
            ],
            [
                'id'    => 213,
                'title' => 'asset_delete',
            ],
            [
                'id'    => 214,
                'title' => 'asset_access',
            ],
            [
                'id'    => 215,
                'title' => 'maintenance_vendor_create',
            ],
            [
                'id'    => 216,
                'title' => 'maintenance_vendor_edit',
            ],
            [
                'id'    => 217,
                'title' => 'maintenance_vendor_show',
            ],
            [
                'id'    => 218,
                'title' => 'maintenance_vendor_delete',
            ],
            [
                'id'    => 219,
                'title' => 'maintenance_vendor_access',
            ],
            [
                'id'    => 220,
                'title' => 'member_status_create',
            ],
            [
                'id'    => 221,
                'title' => 'member_status_edit',
            ],
            [
                'id'    => 222,
                'title' => 'member_status_show',
            ],
            [
                'id'    => 223,
                'title' => 'member_status_delete',
            ],
            [
                'id'    => 224,
                'title' => 'member_status_access',
            ],
            [
                'id'    => 225,
                'title' => 'assets_maintenance_create',
            ],
            [
                'id'    => 226,
                'title' => 'assets_maintenance_edit',
            ],
            [
                'id'    => 227,
                'title' => 'assets_maintenance_show',
            ],
            [
                'id'    => 228,
                'title' => 'assets_maintenance_delete',
            ],
            [
                'id'    => 229,
                'title' => 'assets_maintenance_access',
            ],
            [
                'id'    => 230,
                'title' => 'reminder_create',
            ],
            [
                'id'    => 231,
                'title' => 'reminder_edit',
            ],
            [
                'id'    => 232,
                'title' => 'reminder_show',
            ],
            [
                'id'    => 233,
                'title' => 'reminder_delete',
            ],
            [
                'id'    => 234,
                'title' => 'reminder_access',
            ],
            [
                'id'    => 235,
                'title' => 'profile_password_edit',
            ],
            [
                'id'    => 236,
                'title' => 'member_create',
            ],
            [
                'id'    => 237,
                'title' => 'member_edit',
            ],
            [
                'id'    => 238,
                'title' => 'member_show',
            ],
            [
                'id'    => 239,
                'title' => 'member_delete',
            ],
            [
                'id'    => 240,
                'title' => 'member_access',
            ],
            [
                'id'    => 241,
                'title' => 'service_option_create',
            ],
            [
                'id'    => 242,
                'title' => 'service_option_edit',
            ],
            [
                'id'    => 243,
                'title' => 'service_option_show',
            ],
            [
                'id'    => 244,
                'title' => 'service_option_delete',
            ],
            [
                'id'    => 245,
                'title' => 'service_option_access',
            ],
            [
                'id'    => 246,
                'title' => 'service_options_pricelist_create',
            ],
            [
                'id'    => 247,
                'title' => 'service_options_pricelist_edit',
            ],
            [
                'id'    => 248,
                'title' => 'service_options_pricelist_show',
            ],
            [
                'id'    => 249,
                'title' => 'service_options_pricelist_delete',
            ],
            [
                'id'    => 250,
                'title' => 'service_options_pricelist_access',
            ],
            [
                'id'    => 251,
                'title' => 'freeze_request_create',
            ],
            [
                'id'    => 252,
                'title' => 'freeze_request_edit',
            ],
            [
                'id'    => 253,
                'title' => 'freeze_request_show',
            ],
            [
                'id'    => 254,
                'title' => 'freeze_request_delete',
            ],
            [
                'id'    => 255,
                'title' => 'freeze_request_access',
            ],
            [
                'id'    => 256,
                'title' => 'refund_reason_create',
            ],
            [
                'id'    => 257,
                'title' => 'refund_reason_edit',
            ],
            [
                'id'    => 258,
                'title' => 'refund_reason_show',
            ],
            [
                'id'    => 259,
                'title' => 'refund_reason_delete',
            ],
            [
                'id'    => 260,
                'title' => 'refund_reason_access',
            ],
            [
                'id'    => 261,
                'title' => 'refund_create',
            ],
            [
                'id'    => 262,
                'title' => 'refund_edit',
            ],
            [
                'id'    => 263,
                'title' => 'refund_show',
            ],
            [
                'id'    => 264,
                'title' => 'refund_delete',
            ],
            [
                'id'    => 265,
                'title' => 'refund_access',
            ],
            [
                'id'    => 266,
                'title' => 'account_create',
            ],
            [
                'id'    => 267,
                'title' => 'account_edit',
            ],
            [
                'id'    => 268,
                'title' => 'account_show',
            ],
            [
                'id'    => 269,
                'title' => 'account_delete',
            ],
            [
                'id'    => 270,
                'title' => 'account_access',
            ],
            [
                'id'    => 271,
                'title' => 'external_payment_create',
            ],
            [
                'id'    => 272,
                'title' => 'external_payment_edit',
            ],
            [
                'id'    => 273,
                'title' => 'external_payment_show',
            ],
            [
                'id'    => 274,
                'title' => 'external_payment_delete',
            ],
            [
                'id'    => 275,
                'title' => 'external_payment_access',
            ],
            [
                'id'    => 276,
                'title' => 'withdrawal_create',
            ],
            [
                'id'    => 277,
                'title' => 'withdrawal_edit',
            ],
            [
                'id'    => 278,
                'title' => 'withdrawal_show',
            ],
            [
                'id'    => 279,
                'title' => 'withdrawal_delete',
            ],
            [
                'id'    => 280,
                'title' => 'withdrawal_access',
            ],
            [
                'id'    => 281,
                'title' => 'timeslot_create',
            ],
            [
                'id'    => 282,
                'title' => 'timeslot_edit',
            ],
            [
                'id'    => 283,
                'title' => 'timeslot_show',
            ],
            [
                'id'    => 284,
                'title' => 'timeslot_delete',
            ],
            [
                'id'    => 285,
                'title' => 'timeslot_access',
            ],
            [
                'id'    => 286,
                'title' => 'session_list_create',
            ],
            [
                'id'    => 287,
                'title' => 'session_list_edit',
            ],
            [
                'id'    => 288,
                'title' => 'session_list_show',
            ],
            [
                'id'    => 289,
                'title' => 'session_list_delete',
            ],
            [
                'id'    => 290,
                'title' => 'session_list_access',
            ],
            [
                'id'    => 291,
                'title' => 'schedule_create',
            ],
            [
                'id'    => 292,
                'title' => 'schedule_edit',
            ],
            [
                'id'    => 293,
                'title' => 'schedule_show',
            ],
            [
                'id'    => 294,
                'title' => 'schedule_delete',
            ],
            [
                'id'    => 295,
                'title' => 'schedule_access',
            ],
            [
                'id'    => 296,
                'title' => 'schedule_access',
            ],
            [
                'id'    => 297,
                'title' => 'view_sales_report',
            ],
            [
                'id'    => 298,
                'title' => 'view_freelancers_report',
            ],
            [
                'id'    => 299,
                'title' => 'view_revenue_report',
            ],
            [
                'id'    => 300,
                'title' => 'view_coaches_report',
            ],
            [
                'id'    => 301,
                'title' => 'view_trainers_report',
            ],
            [
                'id'    => 302,
                'title' => 'view_schedule_template',
            ],
            [
                'id'    => 303,
                'title' => 'add_schedule_template',
            ],
            [
                'id'    => 304,
                'title' => 'edit_schedule_template',
            ],
            [
                'id'    => 305,
                'title' => 'delete_schedule_template',
            ],
            [
                'id'    => 306,
                'title' => 'view_attendance_settings',
            ],
            [
                'id'    => 307,
                'title' => 'create_attendance_settings',
            ],
            [
                'id'    => 308,
                'title' => 'view_employee_attendances',
            ],
            [
                'id'    => 309,
                'title' => 'edit_employee_attendances',
            ],
            [
                'id'    => 310,
                'title' => 'delete_employee_attendances',
            ],
            [
                'id'    => 311,
                'title' => 'import_employee_attendances',
            ],
            [
                'id'    => 312,
                'title' => 'view_payroll_page'
            ],
            [
                'id'    => 313,
                'title' => 'expired_membership_access'
            ],
            [
                'id'    => 314,
                'title' => 'markting_access'
            ],
            [
                'id'    => 315,
                'title' => 'whatsapp_access'
            ],
            [
                'id'    => 316,
                'title' => 'sms_access'
            ],
            [
                'id'    => 317,
                'title' => 'email_campaigns_access'
            ],
            [
                'id'    => 318,
                'title' => 'reports_access'
            ],
            [
                'id'    => 319,
                'title' => 'view_schedule_timeline'
            ],
            [
                'id'    => 320,
                'title' => 'view_services_report'
            ],
            [
                'id'    => 321,
                'title' => 'view_offers_report'
            ],
            [
                'id'    => 322,
                'title' => 'view_leads_source_report'
            ],
            [
                'id'    => 323,
                'title' => 'view_member_source_report'
            ],
            [
                'id'    => 324,
                'title' => 'view_daily_report'
            ],
            [
                'id'    => 325,
                'title' => 'view_monthly_report'
            ],
            [
                'id'    => 326,
                'title' => 'view_yearly_finance_report'
            ],
            [
                'id'    => 327,
                'title' => 'view_monthly_finance_report'
            ],
            [
                'id'    => 328,
                'title' => 'view_today_reminders'
            ],
            [
                'id'    => 329,
                'title' => 'view_upcomming_reminders'
            ],
            [
                'id'    => 330,
                'title' => 'view_overdue_reminders'
            ],
            [
                'id'    => 331,
                'title' => 'settings_access'
            ],
            [
                'id'    => 332,
                'title' => 'view_reminders_management'
            ],
            [
                'id'    => 333,
                'title' => 'reason_create'
            ],
            [
                'id'    => 334,
                'title' => 'reason_edit'
            ],
            [
                'id'    => 335,
                'title' => 'reason_show'
            ],
            [
                'id'    => 336,
                'title' => 'reason_delete'
            ],
            [
                'id'    => 337,
                'title' => 'reason_access'
            ],
            [
                'id'    => 338,
                'title' => 'create_campaigns'
            ],
            [
                'id'    => 339,
                'title' => 'edit_campaigns'
            ],
            [
                'id'    => 340,
                'title' => 'access_campaigns'
            ],
            [
                'id'    => 341,
                'title' => 'delete_campaigns'
            ],
            [
                'id'    => 342,
                'title' => 'take_attendance'
            ],
            [
                'id'    => 343,
                'title' => 'data_migration'
            ],
            [
                'id'    => 344,
                'title' => 'help_center'
            ],
            [
                'id'    => 345,
                'title' => 'update_invoice_date'
            ],
            [
                'id'    => 346,
                'title' => 'transfer_to_member'
            ],
            [
                'id'    => 347,
                'title' => 'transfer_to_member'
            ],
            [
                'id'    => 348,
                'title' => 'take_action'
            ],
            [
                'id'    => 349,
                'title' => 'ratings_access'
            ],
            [
                'id'    => 350,
                'title' => 'ratings_show'
            ],
            [
                'id'    => 351,
                'title' => 'due_payments_report'
            ],
            [
                'id'    => 352,
                'title' => 'partial_invoice_access'
            ],
            [
                'id'    => 353,
                'title' => 'view_expenses_report'
            ],
            [
                'id'    => 354,
                'title' => 'lead_filter'
            ],
            [
                'id'    => 355,
                'title' => 'member_filter'
            ],
            [
                'id'    => 356,
                'title' => 'membership_filter'
            ],
            [
                'id'    => 357,
                'title' => 'expenses_filter'
            ],
            [
                'id'    => 358,
                'title' => 'invoice_filter'
            ],
            [
                'id'    => 359,
                'title' => 'payment_filter'
            ],
            [
                'id'    => 360,
                'title' => 'refund_filter'
            ],
            [
                'id'    => 361,
                'title' => 'external_payment_filter'
            ],
            [
                'id'    => 362,
                'title' => 'withdrawal_filter'
            ],
            [
                'id'    => 363,
                'title' => 'employee_filter'
            ],
            [
                'id'    => 364,
                'title' => 'bonu_filter'
            ],
            [
                'id'    => 365,
                'title' => 'deduction_filter'
            ],
            [
                'id'    => 366,
                'title' => 'loan_filter'
            ],
            [
                'id'    => 367,
                'title' => 'lead_counter'
            ],
            [
                'id'    => 368,
                'title' => 'member_counter'
            ],
            [
                'id'    => 369,
                'title' => 'membership_counter'
            ],
            [
                'id'    => 370,
                'title' => 'expenses_counter'
            ],
            [
                'id'    => 371,
                'title' => 'invoice_counter'
            ],
            [
                'id'    => 372,
                'title' => 'payment_counter'
            ],
            [
                'id'    => 373,
                'title' => 'refund_counter'
            ],
            [
                'id'    => 374,
                'title' => 'external_payment_counter'
            ],
            [
                'id'    => 375,
                'title' => 'withdrawal_counter'
            ],
            [
                'id'    => 376,
                'title' => 'employee_counter'
            ],
            [
                'id'    => 377,
                'title' => 'bonu_counter'
            ],
            [
                'id'    => 378,
                'title' => 'deduction_counter'
            ],
            [
                'id'    => 379,
                'title' => 'loan_counter'
            ],
            [
                'id'    => 380,
                'title' => 'vacation_counter'
            ],
            [
                'id'    => 381,
                'title' => 'edit_card_number'
            ],
            [
                'id'    => 382,
                'title' => 'warehouse_create',
            ],
            [
                'id'    => 383,
                'title' => 'warehouse_edit',
            ],
            [
                'id'    => 384,
                'title' => 'warehouse_show',
            ],
            [
                'id'    => 385,
                'title' => 'warehouse_delete',
            ],
            [
                'id'    => 386,
                'title' => 'warehouse_access',
            ],
            [
                'id'    => 387,
                'title' => 'product_create',
            ],
            [
                'id'    => 388,
                'title' => 'product_edit',
            ],
            [
                'id'    => 389,
                'title' => 'product_show',
            ],
            [
                'id'    => 390,
                'title' => 'product_delete',
            ],
            [
                'id'    => 391,
                'title' => 'product_access',
            ],
            [
                'id'    => 392,
                'title' => 'warehouse_product_create',
            ],
            [
                'id'    => 393,
                'title' => 'warehouse_product_edit',
            ],
            [
                'id'    => 394,
                'title' => 'warehouse_product_show',
            ],
            [
                'id'    => 395,
                'title' => 'warehouse_product_delete',
            ],
            [
                'id'    => 396,
                'title' => 'warehouse_product_access',
            ],
            [
                'id'    => 397,
                'title' => 'inventory_access',
            ],
            [
                'id'    => 399,
                'title' => 'add_membership',
            ],
            [
                'id'    => 400,
                'title' => 'master_card_create',
            ],
            [
                'id'    => 401,
                'title' => 'master_card_edit',
            ],
            [
                'id'    => 402,
                'title' => 'master_card_show',
            ],
            [
                'id'    => 403,
                'title' => 'master_card_delete',
            ],
            [
                'id'    => 404,
                'title' => 'master_card_access' ,
            ],
            [
                'id'    => 405,
                'title' => 'member_requests_access',
            ],
            [
                'id'    => 406,
                'title' => 'member_requests_view',
            ],
            [
                'id'    => 407,
                'title' => 'member_requests_create',
            ],
            [
                'id'    => 408,
                'title' => 'member_requests_delete',
            ],
            [
                'id'    => 409,
                'title' => 'member_requests_approve',
            ],
            [
                'id'    => 410,
                'title' => 'approve_reject_freeze',
            ],
            [
                'id'    => 411,
                'title' => 'approve_reject_refund',
            ],
            [
                'id'    => 412,
                'title' => 'view_transfer_sales_data',
            ],
            [
                'id'    => 413,
                'title' => 'view_active_members',
            ],
            [
                'id'    => 414,
                'title' => 'view_inactive_members',
            ],
            [
                'id'    => 415,
                'title' => 'view_freezes',
            ],
            [
                'id'    => 416,
                'title' => 'invitations_report',
            ],
            [
                'id'    => 417,
                'title' => 'view_reminders_report',
            ],
            [
                'id'    => 418,
                'title' => 'view_refund_reasons_report',
            ],
            [
                'id'    => 419,
                'title' => 'invoice_change_date',
            ],
            [
                'id'    => 420,
                'title' => 'free_discount',
            ],
            [
                'id'    => 421,
                'title' => 'editable_end_date',
            ],
            [
                'id'    => 422,
                'title' => 'upgrade_membership',
            ],
            [
                'id'    => 423,
                'title' => 'downgrade_membership',
            ],
            [
                'id'    => 424,
                'title' => 'transfer_membership',
            ],
            [
                'id'    => 425,
                'title' => 'renew_membership',
            ],
            [
                'id'    => 426,
                'title' => 'view_sitemap',
            ],
            [
                'id'    => 427,
                'title' => 'view_expired_memberships_attendances',
            ],
            [
                'id'    => 428,
                'title' => 'trainer_services_access',
            ],
            [
                'id'    => 429,
                'title' => 'trainer_services_view',
            ],
            [
                'id'    => 430,
                'title' => 'trainer_services_create',
            ],
            [
                'id'    => 431,
                'title' => 'trainer_services_delete',
            ],
            [
                'id'    => 432,
                'title' => 'sports_access',
            ],
            [
                'id'    => 433,
                'title' => 'sports_create',
            ],
            [
                'id'    => 434,
                'title' => 'sports_view',
            ],
            [
                'id'    => 435,
                'title' => 'sports_delete',
            ],
            [
                'id'    => 436,
                'title' => 'view_current_memberships_report'
            ],
            [
                'id'    => 437,
                'title' => 'delete_invitations'
            ],
            [
                'id'    => 438,
                'title' => 'delete_notes'
            ],
            [
                'id'    => 439,
                'title' => 'add_notes'
            ],
            [
                'id'    => 440,
                'title' => 'edit_notes'
            ],
            [
                'id'    => 441,
                'title' => 'edit_payment_date'
            ],
            [
                'id'    => 442,
                'title' => 'export_employee_attendances'
            ],
            [
                'id'    => 443,
                'title' => 'export_leads'
            ],
            [
                'id'    => 444,
                'title' => 'export_members'
            ],
            [
                'id'    => 445,
                'title' => 'export_memberships'
            ],
            [
                'id'    => 446,
                'title' => 'export_membership_attendances'
            ],
            [
                'id'    => 447,
                'title' => 'export_member_requests'
            ],
            [
                'id'    => 448,
                'title' => 'export_expenses'
            ],
            [
                'id'    => 449,
                'title' => 'export_invoices'
            ],
            [
                'id'    => 450,
                'title' => 'export_partial_invoices'
            ],
            [
                'id'    => 451,
                'title' => 'export_payments'
            ],
            [
                'id'    => 452,
                'title' => 'export_refunds'
            ],
            [
                'id'    => 453,
                'title' => 'export_active_members'
            ],
            [
                'id'    => 454,
                'title' => 'export_inactive_members'
            ],
            [
                'id'    => 455,
                'title' => 'export_invitations'
            ],
            [
                'id'    => 456,
                'title' => 'export_expired_memberships'
            ],
            [
                'id'    => 457,
                'title' => 'export_expiring_memberships'
            ],
            [
                'id'    => 458,
                'title' => 'export_freezes'
            ],
            [
                'id'    => 459,
                'title' => 'export_revenues'
            ],
            [
                'id'    => 460,
                'title' => 'export_coaches'
            ],
            [
                'id'    => 461,
                'title' => 'export_trainers'
            ],
            [
                'id'    => 462,
                'title' => 'expiring_membership_access'
            ],
            [
                'id'    => 463,
                'title' => 'reminders_history'
            ],
            [
                'id'    => 464,
                'title' => 'edit_member_code'
            ],
            [
                'id'    => 465,
                'title' => 'lead_import'
            ],
            [
                'id'    => 466,
                'title' => 'member_import'
            ],
            [
                'id'    => 467,
                'title' => 'settlement_invoice_access'
            ],
            [
                'id'    => 468,
                'title' => 'view_reminders_actions_report'
            ],
            [
                'id'    => 469,
                'title' => 'branch_create',
            ],
            [
                'id'    => 470,
                'title' => 'branch_edit',
            ],
            [
                'id'    => 471,
                'title' => 'branch_show',
            ],
            [
                'id'    => 472,
                'title' => 'branch_delete',
            ],
            [
                'id'    => 473,
                'title' => 'branch_access',
            ],
            [
                'id'    => 474,
                'title' => 'external_payment_category_create',
            ],
            [
                'id'    => 475,
                'title' => 'external_payment_category_edit',
            ],
            [
                'id'    => 476,
                'title' => 'external_payment_category_show',
            ],
            [
                'id'    => 477,
                'title' => 'external_payment_category_delete',
            ],
            [
                'id'    => 478,
                'title' => 'external_payment_category_access',
            ],
            [
                'id'    => 479,
                'title' => 'schedule_main_create',
            ],
            [
                'id'    => 480,
                'title' => 'schedule_main_edit',
            ],
            [
                'id'    => 481,
                'title' => 'schedule_main_show',
            ],
            [
                'id'    => 482,
                'title' => 'schedule_main_delete',
            ],
            [
                'id'    => 483,
                'title' => 'schedule_main_access',
            ],
            [
                'id'    => 484,
                'title' => 'edit_sales_by',
            ],
            [
                'id'    => 485,
                'title' => 'assign_reminder',
            ],
            [
                'id'    => 486,
                'title' => 'task_access',
            ],
            [
                'id'    => 487,
                'title' => 'task_create',
            ],
            [
                'id'    => 488,
                'title' => 'task_edit',
            ],
            [
                'id'    => 489,
                'title' => 'task_show',
            ],
            [
                'id'    => 490,
                'title' => 'task_delete',
            ],
            [
                'id'    => 491,
                'title' => 'task_action',
            ],
            [
                'id'    => 492,
                'title' => 'view_sales_manager_report',
            ],
            [
                'id'    => 493,
                'title' => 'view_onhold_members',
            ],
            [
                'id'    => 494,
                'title' => 'view_customer_invitations_report',
            ],
            [
                'id'    => 495,
                'title' => 'view_dayuse_members_report',
            ],
            [
                'id'    => 496,
                'title' => 'view_guest_log_report',
            ],
            [
                'id'    => 497,
                'title' => 'view_daily_task_report',
            ],
            [
                'id'    => 498,
                'title' => 'view_actions_report',
            ],
            [
                'id'    => 499,
                'title' => 'view_main_expired_memberships_report',
            ],
            [
                'id'    => 500,
                'title' => 'view_pt_expired_memberships_report',
            ],
            [
                'id'    => 501,
                'title' => 'view_other_revenue_categroies_report',
            ],
            [
                'id'    => 502,
                'title' => 'view_tax_accountant_report',
            ],
            [
                'id'    => 503,
                'title' => 'view_trainer_reminders_report',
            ],
            [
                'id'    => 504,
                'title' => 'view_trainer_reminders_histories_report',
            ],
            [
                'id'    => 505,
                'title' => 'block_member',
            ],
            [
                'id'    => 506,
                'title' => 'invite_member',
            ],
            [
                'id'    => 507,
                'title' => 'view_trainers_reminders_actions_report',
            ],
            [
                'id'    => 508,
                'title' => 'assigned_membership',
            ],
            [
                'id'    => 509,
                'title' => 'sales_reminders',
            ],
            [
                'id'    => 510,
                'title' => 'trainer_reminders',
            ],
            [
                'id'    => 511,
                'title' => 'view_pt_attendances',
            ],
            [
                'id'    => 512,
                'title' => 'view_daily_trainer_report',
            ],
            [
                'id'    => 513,
                'title' => 'view_fitness_manager_report',
            ],
            [
                'id'    => 514,
                'title' => 'zoom_access',
            ],
            [
                'id'    => 515,
                'title' => 'zoom_create',
            ],
            [
                'id'    => 516,
                'title' => 'zoom_edit',
            ],
            [
                'id'    => 517,
                'title' => 'zoom_show',
            ],
            [
                'id'    => 518,
                'title' => 'zoom_delete',
            ],
        ];

        Permission::insert($permissions);
    }
}
