<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (\App\Models\Customer::count() == 0) {
            \App\Models\Customer::create(['name' => 'Adarsh Kumar', 'email' => 'adarsh@example.com', 'phone' => '9887766554', 'address' => '12, MG Road, Bangalore', 'gst_number' => '29AAAAA0000A1Z5']);
            \App\Models\Customer::create(['name' => 'Priya Singh', 'email' => 'priya@example.com', 'phone' => '9123456789', 'address' => '45, Nehry Place, Delhi']);
            \App\Models\Customer::create(['name' => 'City Hospital Tech', 'email' => 'admin@cityhospital.com', 'phone' => '044-24567890', 'address' => 'Chennai, TN', 'gst_number' => '33BBBBB1111B1Z9']);
        }

        if (\App\Models\Product::count() == 0) {
            \App\Models\Product::create(['name' => 'General Consultation', 'price' => 500.00, 'description' => 'Doctor Consultation Fee', 'hsn_code' => '9993']);
            \App\Models\Product::create(['name' => 'X-Ray Chest PA View', 'price' => 800.00, 'description' => 'Radiology Service', 'hsn_code' => '9993']);
            \App\Models\Product::create(['name' => 'Blood Test - CBC', 'price' => 450.00, 'description' => 'Pathology Lab', 'hsn_code' => '9993']);
            \App\Models\Product::create(['name' => 'Paracetamol 500mg (Strip)', 'price' => 35.00, 'description' => 'Medicine', 'hsn_code' => '3004', 'stock' => 500]);
            \App\Models\Product::create(['name' => 'Room Charge (Private)', 'price' => 4500.00, 'description' => 'Inpatient Services', 'hsn_code' => '9993']);
        }
    }
}
